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
                                            <div class="admin-user-actions">
                                                @if ($user->role === 'consumer' && !$user->is_blocked)
                                                    <a class="admin-action-button admin-action-vendor" href="{{ route('admin.users', ['search' => $searchTerm, 'role' => $selectedRole, 'status' => $selectedStatus, 'sort' => $selectedSort, 'promote_email' => $user->email]) }}#grant-vendor">Make Vendor</a>
                                                @endif

                                                @if ($user->role === 'vendor')
                                                    <form method="POST" action="{{ route('admin.users.revoke-vendor', $user) }}" onsubmit="return confirm('Revoke vendor access and revert this account back to consumer?');">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="admin-action-button admin-action-revoke" type="submit">Revoke</button>
                                                    </form>
                                                @endif

                                                <form method="POST" action="{{ route('admin.users.toggle-block', $user) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    @if ($user->is_blocked)
                                                        <button class="admin-action-button admin-action-unblock" type="submit">Unblock</button>
                                                    @else
                                                        <button class="admin-action-button admin-action-block" type="submit">Block</button>
                                                    @endif
                                                </form>
                                            </div>
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

            <section class="admin-card admin-light-card admin-light-card-green" id="grant-vendor" aria-labelledby="grant-vendor-title">
                <div class="admin-card-header">
                    <h2 id="grant-vendor-title">Grant Vendor Access</h2>
                    <p>Enter a consumer email (or click Make Vendor above) and assign one vendor outlet</p>
                </div>

                <form class="admin-vendor-grant-form" method="POST" action="{{ route('admin.users.grant-vendor') }}">
                    @csrf

                    <div class="admin-form-grid">
                        <div class="admin-form-field">
                            <label for="consumer_email">Consumer email</label>
                            @if (old('consumer_email'))
                                <input id="consumer_email" type="email" name="consumer_email" value="{{ old('consumer_email') }}" placeholder="Example: ali@example.com" required>
                            @else
                                <input id="consumer_email" type="email" name="consumer_email" value="{{ $selectedConsumerEmail }}" placeholder="Example: ali@example.com" required>
                            @endif
                            @if ($errors->has('consumer_email'))
                                <p class="admin-field-error">{{ $errors->first('consumer_email') }}</p>
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
                            <select id="outlet_location" name="outlet_location" required>
                                <option value="">Select location</option>
                                @foreach ($outletLocations as $locationOption)
                                    @if (old('outlet_location') === $locationOption)
                                        <option value="{{ $locationOption }}" selected>{{ $locationOption }}</option>
                                    @else
                                        <option value="{{ $locationOption }}">{{ $locationOption }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @if ($errors->has('outlet_location'))
                                <p class="admin-field-error">{{ $errors->first('outlet_location') }}</p>
                            @endif
                        </div>
                    </div>

                    @if (old('outlet_location') === 'Others')
                        <div class="admin-form-field admin-custom-location-field" id="custom-location-wrapper">
                    @else
                        <div class="admin-form-field admin-custom-location-field" id="custom-location-wrapper" hidden>
                    @endif
                            <label for="custom_outlet_location">Specify other location</label>
                            <input id="custom_outlet_location" type="text" name="custom_outlet_location" value="{{ old('custom_outlet_location') }}" placeholder="Example: Kajang Prima">
                            @if ($errors->has('custom_outlet_location'))
                                <p class="admin-field-error">{{ $errors->first('custom_outlet_location') }}</p>
                            @endif
                        </div>

                    <button class="admin-green-submit-button" type="submit">Approve as Vendor</button>
                </form>
            </section>
        </div>
    </section>

    <script>
        // Shows the custom location input only when "Others" is chosen in Outlet location
        const outletLocationSelect = document.getElementById('outlet_location');
        const customLocationWrapper = document.getElementById('custom-location-wrapper');
        const customLocationInput = document.getElementById('custom_outlet_location');

        function toggleCustomLocationField() {
            if (outletLocationSelect.value === 'Others') {
                customLocationWrapper.hidden = false;
                customLocationInput.required = true;
            } else {
                customLocationWrapper.hidden = true;
                customLocationInput.required = false;
            }
        }

        outletLocationSelect.addEventListener('change', toggleCustomLocationField);
        toggleCustomLocationField();
    </script>
@endsection
