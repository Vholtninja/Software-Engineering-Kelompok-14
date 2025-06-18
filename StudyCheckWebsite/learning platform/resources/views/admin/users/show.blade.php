<x-app-layout>
    <x-slot name="title">{{ $user->name }} - User Details - Admin - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body text-center">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar avatar-lg mb-3">
                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">{{ $user->email }}</p>
                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'moderator' ? 'warning' : ($user->role === 'teacher' ? 'info' : ($user->role === 'expert' ? 'success' : 'secondary'))) }} fs-6 mb-3">
                            {{ ucfirst($user->role) }}
                        </span>
                        
                        @if($user->bio)
                            <p class="text-muted">{{ $user->bio }}</p>
                        @endif

                        @if($user->institution)
                            <div class="mb-2">
                                <i class="bi bi-building me-2 text-muted"></i>
                                <span class="text-muted">{{ $user->institution }}</span>
                            </div>
                        @endif

                        <div class="mb-3">
                            <i class="bi bi-trophy me-2 text-warning"></i>
                            <span class="fw-bold">{{ number_format($user->points) }}</span> points
                        </div>

                        <div class="mb-3">
                            <span class="badge bg-info">{{ ucfirst($user->level) }} Level</span>
                            @if($user->is_verified)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Verified
                                </span>
                            @else
                                <span class="badge bg-warning">
                                    <i class="bi bi-clock me-1"></i>Unverified
                                </span>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                                <i class="bi bi-pencil me-2"></i>Edit User
                            </a>
                            @if($user->id !== auth()->id())
                                @if($user->is_verified)
                                    <form method="POST" action="{{ route('admin.users.unverify', $user) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-warning w-100">Unverify User</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">Verify User</button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">Account Info</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                        <p><strong>Last Updated:</strong> {{ $user->updated_at->format('M d, Y') }}</p>
                        <p><strong>Email Verified:</strong> 
                            @if($user->email_verified_at)
                                <span class="text-success">{{ $user->email_verified_at->format('M d, Y') }}</span>
                            @else
                                <span class="text-danger">Not verified</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-book display-6 mb-2"></i>
                                <h3>{{ $user->courses->count() }}</h3>
                                <small>Courses {{ $user->role === 'student' ? 'Enrolled' : 'Created' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-chat-dots display-6 mb-2"></i>
                                <h3>{{ $user->forumThreads->count() }}</h3>
                                <small>Forum Threads</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-chat display-6 mb-2"></i>
                                <h3>{{ $user->forumReplies->count() }}</h3>
                                <small>Forum Replies</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <i class="bi bi-question-circle display-6 mb-2"></i>
                                <h3>{{ $user->homework->count() }}</h3>
                                <small>Homework Questions</small>
                            </div>
                        </div>
                    </div>
                </div>

                @if($user->role === 'student')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Enrolled Courses</h5>
                        </div>
                        <div class="card-body">
                            @if($user->enrollments->count() > 0)
                                @foreach($user->enrollments as $course)
                                    <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $course->title }}</h6>
                                            <small class="text-muted">by {{ $course->instructor->name }}</small>
                                            @if(isset($course->pivot))
                                                <div class="progress mt-2" style="height: 6px;">
                                                    <div class="progress-bar" style="width: {{ $course->pivot->progress_percentage }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ $course->pivot->progress_percentage }}% completed</small>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center">No enrolled courses</p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Created Courses</h5>
                        </div>
                        <div class="card-body">
                            @if($user->courses->count() > 0)
                                @foreach($user->courses as $course)
                                    <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $course->title }}</h6>
                                            <small class="text-muted">{{ $course->category }} • {{ ucfirst($course->level) }}</small>
                                            <div class="mt-1">
                                                <span class="badge bg-{{ $course->is_active ? 'success' : 'secondary' }}">
                                                    {{ $course->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center">No created courses</p>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Forum Activity</h5>
                    </div>
                    <div class="card-body">
                        @if($user->forumThreads->count() > 0)
                            @foreach($user->forumThreads->take(5) as $thread)
                                <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <h6 class="mb-1">
                                        <a href="{{ route('forum.thread', [$thread->category->slug, $thread->slug]) }}" class="text-decoration-none">
                                            {{ $thread->title }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        in {{ $thread->category->name }} • {{ $thread->created_at->diffForHumans() }}
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-eye me-1"></i>{{ $thread->views }}
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-chat me-1"></i>{{ $thread->replies_count }}
                                    </small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No forum activity</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>