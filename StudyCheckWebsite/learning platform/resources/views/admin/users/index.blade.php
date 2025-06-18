<x-app-layout>
    <x-slot name="title">User Management - Admin - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>User Management</h2>
                <p class="text-muted">Manage all registered users</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Add User
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select">
                                <option value="">All Roles</option>
                                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                                <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                <option value="expert" {{ request('role') === 'expert' ? 'selected' : '' }}>Expert</option>
                                <option value="moderator" {{ request('role') === 'moderator' ? 'selected' : '' }}>Moderator</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="verified" class="form-select">
                                <option value="">All Status</option>
                                <option value="1" {{ request('verified') === '1' ? 'selected' : '' }}>Verified</option>
                                <option value="0" {{ request('verified') === '0' ? 'selected' : '' }}>Unverified</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if($users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Institution</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar avatar-sm me-3">
                                                <div>
                                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'moderator' ? 'warning' : ($user->role === 'teacher' ? 'info' : ($user->role === 'expert' ? 'success' : 'secondary'))) }}">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>{{ $user->institution ?? '-' }}</td>
                                        <td>
                                            @if($user->is_verified)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Verified
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-clock me-1"></i>Unverified
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-primary">View</a>
                                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-secondary">Edit</a>
                                                @if($user->id !== auth()->id())
                                                    @if($user->is_verified)
                                                        <form method="POST" action="{{ route('admin.users.unverify', $user) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-warning">Unverify</button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('admin.users.verify', $user) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-success">Verify</button>
                                                        </form>
                                                    @endif
                                                    <button class="btn btn-outline-danger" onclick="deleteUser({{ $user->id }})">Delete</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $users->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-people display-1 text-muted"></i>
                        <h4 class="text-muted mt-3">No users found</h4>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/users/${userId}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    @endpush
</x-app-layout>