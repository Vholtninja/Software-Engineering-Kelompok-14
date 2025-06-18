<x-app-layout>
    <x-slot name="title">Admin Dashboard - {{ config('app.name') }}</x-slot>

    <div class="container py-5">
        <div class="mb-5">
            <h1 class="fw-bold">Admin Dashboard</h1>
            <p class="text-muted fs-5">Welcome, {{ auth()->user()->name }}. Manage your platform from here.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold">{{ $stats['total_users'] }}</div>
                            <div class="text-muted fw-semibold">Total Users</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-success text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-journal-check fs-3"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold">{{ $stats['total_courses'] }}</div>
                            <div class="text-muted fw-semibold">Total Courses</div>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-info text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-chat-dots-fill fs-3"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold">{{ $stats['total_threads'] }}</div>
                            <div class="text-muted fw-semibold">Forum Threads</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning text-white rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-check-fill fs-3"></i>
                        </div>
                        <div>
                            <div class="fs-4 fw-bold">{{ $stats['pending_verifications'] }}</div>
                            <div class="text-muted fw-semibold">Pending Verifications</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row g-4 mt-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Management Tools</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="list-group list-group-flush">
                            <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-people-fill fs-4 me-3 text-primary"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold">User Management</h6>
                                        <small class="text-muted">Manage all user accounts, roles, and permissions.</small>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="{{ route('admin.forum-categories.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-tags-fill fs-4 me-3 text-danger"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Manage Forum Categories</h6>
                                        <small class="text-muted">Create, edit, or delete forum categories.</small>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                            <a href="{{ route('courses.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-journal-bookmark-fill fs-4 me-3 text-success"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Course Management</h6>
                                        <small class="text-muted">Review, edit, or remove courses from the platform.</small>
                                    </div>
                                </div>
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                     <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Recent Registrations</h5>
                    </div>
                    <div class="card-body">
                         <div class="list-group list-group-flush">
                             @forelse ($recentUsers as $user)
                                <a href="{{ route('admin.users.show', $user) }}" class="list-group-item list-group-item-action px-0">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $user->avatar_url }}" class="avatar me-3" alt="{{ $user->name }}">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                    </div>
                                </a>
                            @empty
                                <p class="text-muted">No new users recently.</p>
                            @endforelse
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>