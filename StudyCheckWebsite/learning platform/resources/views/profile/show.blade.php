<x-app-layout>
    <x-slot name="title">{{ $user->name }} - Profile - {{ config('app.name') }}</x-slot>

    @push('styles')
    <style>
        .profile-header {
            background-color: var(--bs-dark);
            color: white;
            padding: 3rem 0;
        }
        .profile-avatar {
            width: 128px;
            height: 128px;
            border: 4px solid white;
            margin-top: -64px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
    </style>
    @endpush

    <div class="profile-header">
        <div class="container text-center text-lg-start">
        </div>
    </div>
    
    <div class="container">
        <div class="d-flex flex-column flex-lg-row align-items-center align-items-lg-end">
            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="avatar profile-avatar">
            <div class="ms-lg-4 text-center text-lg-start mt-3 mt-lg-0">
                <h2 class="fw-bold d-flex align-items-center gap-2">
                    {{ $user->name }}
                    @if($user->is_verified)
                        <i class="bi bi-patch-check-fill text-primary" title="Verified Account"></i>
                    @endif
                </h2>
                <p class="text-muted">{{ $user->email }}</p>
            </div>
            @if(auth()->check() && auth()->id() === $user->id)
                <div class="ms-lg-auto mt-3 mt-lg-0">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="bi bi-gear-fill me-2"></i>Edit Profile & Settings
                    </a>
                </div>
            @endif
        </div>
        
        <div class="row g-4 mt-4">
            <div class="col-lg-4">
                 <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold">About Me</h5>
                        <p class="text-muted">{{ $user->bio ?? 'No bio available.' }}</p>
                        <hr>
                        <ul class="list-unstyled d-grid gap-2">
                            <li><i class="bi bi-briefcase-fill me-2 text-primary"></i> {{ ucfirst($user->role) }}</li>
                            @if($user->institution)
                                <li><i class="bi bi-building-fill me-2 text-primary"></i> {{ $user->institution }}</li>
                            @endif
                            <li><i class="bi bi-bar-chart-fill me-2 text-primary"></i> {{ ucfirst($user->level) }} Level</li>
                             <li><i class="bi bi-trophy-fill me-2 text-warning"></i> {{ number_format($user->points) }} Points</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                         <h5 class="fw-bold mb-0">{{ $user->role === 'student' ? 'My Learning Activity' : 'My Contribution' }}</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold">
                            {{ ($user->role === 'student' || $user->role === 'expert') ? 'Recently Enrolled Courses' : 'Recently Created Courses' }}
                        </h6>
                         @if($recentCourses->count() > 0)
                            @foreach($recentCourses as $course)
                                <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">
                                            <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none">{{ $course->title }}</a>
                                        </h6>
                                        <small class="text-muted">
                                            {{ $course->category }} • {{ ucfirst($course->level) }}
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                        {{ ($user->role === 'student' || $user->role === 'expert') && isset($course->pivot) ? 'Enrolled ' . \Carbon\Carbon::parse($course->pivot->created_at)->diffForHumans() : 'Created ' . $course->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No course activity yet.</p>
                        @endif

                        <hr>

                        <h6 class="fw-bold">Recent Forum Activity</h6>
                        @if($recentThreads->count() > 0)
                             @foreach($recentThreads as $thread)
                                <div class="d-flex align-items-start mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="rounded-circle me-3" style="width: 40px; height: 40px; background-color: {{ $thread->category->color }};">
                                        <i class="bi bi-chat-dots-fill text-white d-flex align-items-center justify-content-center h-100"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">
                                            <a href="{{ route('forum.thread', [$thread->category->slug, $thread->slug]) }}" class="text-decoration-none">
                                                {{ $thread->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            in {{ $thread->category->name }} • {{ $thread->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No forum activity yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>