<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VendorOutlet;
use App\Services\AdminSummaryService;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    protected $adminSummaryService;

    // Shared AdminSummaryService.php - supplies the 4 top KPI cards in admin/panel-overview.blade
    public function __construct(AdminSummaryService $adminSummaryService)
    {
        $this->adminSummaryService = $adminSummaryService;
    }

    // Connected view: admin/users.blade.php
    public function users()
    {
        // this function is called when user click "Users" tab or submits Search/Filters in admin/users.blade, referring to web.php GET (/admin/users)
        // it loads paginated users (filtered by search/role/status and sorted) and active consumers, then opens admin/users.blade (in the return view below)

        $summaryStatistics = $this->adminSummaryService->getSummaryStatistics();
        $searchTerm = request('search');                            // search from "name=search" in admin/users.blade
        $selectedRole = $this->normalizeRoleFilter(request('role'));    //normalizeRoleFilter() = allows only valid role filter values from the Role dropdown in admin/users.blade
        $selectedStatus = $this->normalizeStatusFilter(request('status'));
        $selectedSort = $this->normalizeSortOption(request('sort'));

        // specifically for admin/users.blade (line 52). bcs we need user to hold vendorOutlet if they have one for that blade section
        $userQuery = User::with('vendorOutlet');                    // with('vendorOutlet') = after finding all users, also show their vendorOutlet (Vendor) ONLY IF they have one

        // % for similarity search, like a "contains" search
        if ($searchTerm) {
            $userQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $searchTerm . '%')
                    ->orWhere('role', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($selectedRole) {
            $userQuery->where('role', $selectedRole);
        }

        if ($selectedStatus === 'active') {
            $userQuery->where('is_blocked', false);
        }

        if ($selectedStatus === 'blocked') {
            $userQuery->where('is_blocked', true);
        }

        if ($selectedSort === 'name_desc') {
            $userQuery->orderByDesc('name');
        } elseif ($selectedSort === 'latest') {
            $userQuery->orderByDesc('created_at');
        } elseif ($selectedSort === 'oldest') {
            $userQuery->orderBy('created_at');
        } else {
            $userQuery->orderBy('name');
        }

        // withQueryString() = keeps active search, filters, and sort when changing pages
        $users = $userQuery->paginate(10)->withQueryString();

        // Active consumer eligible to be approved as vendors with an assigned outlet || refer to admin/users.blade (line 107 or search is_blocked)
        $consumerUsers = User::where('role', 'consumer')
            ->where('is_blocked', false)
            ->orderBy('name')
            ->get();

        return view('admin.users', [
            'summaryStatistics' => $summaryStatistics,
            'users' => $users,
            'consumerUsers' => $consumerUsers,
            'searchTerm' => $searchTerm,
            'selectedRole' => $selectedRole,
            'selectedStatus' => $selectedStatus,
            'selectedSort' => $selectedSort,
        ]);
    }

    // Allows only valid role filter values from the Role dropdown
    private function normalizeRoleFilter($requestedRole)    // $requestedRole is from the request('role') in the users() function above, which is from the Role dropdown in admin/users.blade
    {
        $allowedRoles = ['consumer', 'vendor', 'admin'];

        if (in_array($requestedRole, $allowedRoles, true)) {
            return $requestedRole;
        }

        return null;
    }

    // Allows only valid status filter values from the Status dropdown
    private function normalizeStatusFilter($requestedStatus)
    {
        $allowedStatuses = ['active', 'blocked'];

        if (in_array($requestedStatus, $allowedStatuses, true)) {
            return $requestedStatus;
        }

        return null;
    }

    // Allows only valid sort choices from the Sort dropdown (defaults to Name A-Z)
    private function normalizeSortOption($requestedSort)
    {
        $allowedSorts = ['name_asc', 'name_desc', 'latest', 'oldest'];

        if (in_array($requestedSort, $allowedSorts, true)) {
            return $requestedSort;
        }

        return 'name_asc';
    }

    // function is called when admin click "Block" or "Unblock" button in admin/users.blade and it refer to web.php PATCH then opens this function
    // e.g; "block" button clicked, it will change/toggle current user is_blocked status
    public function toggleUserBlock(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin accounts cannot be blocked.');
        }

        // NOT(user is blocked) || REFER EXAMPLE ABOVE
        $newBlockedStatus = !$user->is_blocked; // is_blocked uses boolean on DB

        $user->update([
            'is_blocked' => $newBlockedStatus,
        ]);

        // web.php refer to User ID, so it automatically detect which user opened the page
        if ($newBlockedStatus) {
            return redirect()->route('admin.users')->with('success', $user->name . ' has been blocked.');
        }

        return redirect()->route('admin.users')->with('success', $user->name . ' has been unblocked.');
    }

    // this function is called when admin clicks "Approve as Vendor" button in admin/users.blade, referring to web.php POST (/admin/users/grant-vendor)
    public function grantVendorAccess(Request $request) // Request $request = to get the form inputs from admin/users.blade (user_id, outlet_name, outlet_location)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'outlet_name' => ['required', 'string', 'max:255'],
            'outlet_location' => ['required', 'string', 'max:255'],
        ]);

        $selectedUser = User::findOrFail($validated['user_id']);

        if ($selectedUser->role !== 'consumer') {
            return back()->withInput()->with('error', 'Only consumer accounts can be upgraded to a vendor.');   //withInput() = keeps the form input values after redirect back to the form page
        }

        if ($selectedUser->vendorOutlet) {
            return back()->withInput()->with('error', 'This user already manages a vendor outlet.');
        }

        $selectedUser->update([
            'role' => 'vendor',
        ]);

        VendorOutlet::create([
            'user_id' => $selectedUser->id,
            'outlet_name' => $validated['outlet_name'],
            'outlet_location' => $validated['outlet_location'],
        ]);

        // it validates the form, changes selected consumer's role to 'vendor', and creates their row in vendor_outlets table
        return redirect()->route('admin.users')
            ->with('success', $selectedUser->name . ' has been approved as a vendor for ' . $validated['outlet_name'] . '.');
    }
}
