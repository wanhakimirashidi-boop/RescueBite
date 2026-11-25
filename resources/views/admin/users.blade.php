{{-- Admin Users Management (RETURN VIEW) --}}

@extends('layouts.admin')

@section('title', 'Users | RescueBite Admin Panel')

@section('content')
    @include('admin.partials.panel-overview', ['currentTab' => 'users'])

    <section class="admin-tab-section">
        <div class="container">
            @if (session('success'))
                <p class="admin-success-message">{{ session('success') }}</p>
            @endif

            @if (session('error'))
                <p class="admin-error-message">{{ session('error') }}</p>
            @endif

            <section class="admin-card admin-light-card admin-users-card" aria-labelledby="users-list-title">
                <div class="admin-card-header admin-card-header-split">
                    <div>
                        <h2 id="users-list-title">User Accounts</h2>
                        <p>Manage registered consumers, vendors, and account access</p>
                    </div>
                    <span class="admin-light-count-badge">{{ $users->total() }} users</span>
                </div>

                <form class="admin-users-filter-bar" method="GET" action="{{ route('admin.users') }}">
                    <div class="admin-form-field admin-users-search-field">
                        <label for="admin-user-search">Search user</label>
                        <input id="admin-user-search" type="search" name="search" value="{{ $searchTerm }}" placeholder="Name, email, or role">
                    </div>

                    <div class="admin-form-field">
                        <label for="admin-user-role">Role</label>
                        <select id="admin-user-role" name="role">
                            <option value="">All roles</option>
                            @if ($selectedRole === 'consumer')
                                <option value="consumer" selected>Consumer</option>
                            @else
                                <option value="consumer">Consumer</option>
                            @endif

                            @if ($selectedRole === 'vendor')
                                <option value="vendor" selected>Vendor</option>
                            @else
                                <option value="vendor">Vendor</option>
                            @endif

                            @if ($selectedRole === 'admin')
                                <option value="admin" selected>Admin</option>
                            @else
                                <option value="admin">Admin</option>
                            @endif
                        </select>
                    </div>

                    <div class="admin-form-field">
                        <label for="admin-user-status">Status</label>
                        <select id="admin-user-status" name="status">
                            <option value="">All statuses</option>
                            @if ($selectedStatus === 'active')
                                <option value="active" selected>Active</option>
                            @else
                                <option value="active">Active</option>
                            @endif

                            @if ($selectedStatus === 'blocked')
                                <option value="blocked" selected>Blocked</option>
                            @else
                                <option value="blocked">Blocked</option>
                            @endif
                        </select>
                    </div>

                    <div class="admin-form-field">
                        <label for="admin-user-sort">Sort by</label>
                        <select id="admin-user-sort" name="sort">
                            @if ($selectedSort === 'name_asc')
                                <option value="name_asc" selected>User (A - Z)</option>
                            @else
                                <option value="name_asc">User (A - Z)</option>
                            @endif

                            @if ($selectedSort === 'name_desc')
                                <option value="name_desc" selected>User (Z - A)</option>
                            @else
                                <option value="name_desc">User (Z - A)</option>
                            @endif

                            @if ($selectedSort === 'latest')
                                <option value="latest" selected>Latest joined</option>
                            @else
                                <option value="latest">Latest joined</option>
                            @endif

                            @if ($selectedSort === 'oldest')
                                <option value="oldest" selected>Oldest joined</option>
                            @else
                                <option value="oldest">Oldest joined</option>
                            @endif
                        </select>
                    </div>

                    <div class="admin-users-filter-actions">
                        <button class="admin-submit-button" type="submit">Apply</button>
                        @if ($searchTerm || $selectedRole || $selectedStatus || $selectedSort !== 'name_asc')
                            <a class="admin-light-reset-button" href="{{ route('admin.users') }}">Reset</a>
                        @endif
                    </div>
                </form>

                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th class="admin-cell-center">Role</th>
                                <th class="admin-cell-center">Status</th>
                                <th class="admin-cell-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>
                                        <span class="admin-cell-strong">{{ $user->name }}</span>
                                        {{-- if user is a vendor, display their outlet information. Else skip --}}
                                        @if ($user->vendorOutlet)
                                            <span class="admin-user-outlet">{{ $user->vendorOutlet->outlet_name }} ({{ $user->vendorOutlet->outlet_location }})</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td class="admin-cell-center">
                                        @if ($user->role === 'admin')
                                            <span class="admin-role-badge admin-role-admin">Admin</span>
                                        @elseif ($user->role === 'vendor')
                                            <span class="admin-role-badge admin-role-vendor">Vendor</span>
                                        @else
                                            <span class="admin-role-badge admin-role-consumer">Consumer</span>
                                        @endif
                                    </td>
                                    <td class="admin-cell-center">
                                        @if ($user->is_blocked)
                                            <span class="admin-status-badge admin-status-blocked-soft">Blocked</span>
                                        @else
                                            <span class="admin-status-badge admin-status-active-soft">Active</span>
                                        @endif
                                    </td>
                                    <td class="admin-cell-center">
                                        @if ($user->role === 'admin')
                                            <span class="admin-cell-muted">Protected</span>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.toggle-block', $user) }}">
                                                @csrf
                                                @method('PATCH')
                                                @if ($user->is_blocked)
                                                    <button class="admin-action-button admin-action-unblock" type="submit">Unblock</button>
                                                @else
                                                    <button class="admin-action-button admin-action-block" type="submit">Block</button>
                                                @endif
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="admin-empty-cell">No matching users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @include('admin.partials.pagination', ['records' => $users, 'paginationLabel' => 'Users pages'])
            </section>

            <section class="admin-card admin-light-card admin-light-card-green" aria-labelledby="grant-vendor-title">
                <div class="admin-card-header">
                    <h2 id="grant-vendor-title">Grant Vendor Access</h2>
                    <p>Approve a consumer account and assign one vendor outlet</p>
                </div>

                @if ($consumerUsers->count() > 0)
                    <form class="admin-vendor-grant-form" method="POST" action="{{ route('admin.users.grant-vendor') }}">
                        @csrf

                        <div class="admin-form-grid">
                            <div class="admin-form-field">
                                <label for="user_id">Consumer account</label>
                                <select id="user_id" name="user_id" required>
                                    <option value="">Select a consumer</option>
                                    @foreach ($consumerUsers as $consumerUser)
                                        @if (old('user_id') == $consumerUser->id)
                                            <option value="{{ $consumerUser->id }}" selected>{{ $consumerUser->name }} ({{ $consumerUser->email }})</option>
                                        @else
                                            <option value="{{ $consumerUser->id }}">{{ $consumerUser->name }} ({{ $consumerUser->email }})</option>
                                        @endif
                                    @endforeach
                                </select>
                                @if ($errors->has('user_id'))
                                    <p class="admin-field-error">{{ $errors->first('user_id') }}</p>
                                @endif
                            </div>

                            <div class="admin-form-field">
                                <label for="outlet_name">Outlet name</label>
                                <input id="outlet_name" type="text" name="outlet_name" value="{{ old('outlet_name') }}" placeholder="Example: Cafe Harmoni" required>
                                @if ($errors->has('outlet_name'))
                                    <p class="admin-field-error">{{ $errors->first('outlet_name') }}</p>
                                @endif
                            </div>

                            <div class="admin-form-field">
                                <label for="outlet_location">Outlet location</label>
                                <input id="outlet_location" type="text" name="outlet_location" value="{{ old('outlet_location') }}" placeholder="Example: UNITEN Putrajaya" required>
                                @if ($errors->has('outlet_location'))
                                    <p class="admin-field-error">{{ $errors->first('outlet_location') }}</p>
                                @endif
                            </div>
                        </div>

                        <button class="admin-green-submit-button" type="submit">Approve as Vendor</button>
                    </form>
                @else
                    <p class="admin-empty-note">There are no active consumer accounts waiting for vendor assignment right now.</p>
                @endif
            </section>
        </div>
    </section>
@endsection
