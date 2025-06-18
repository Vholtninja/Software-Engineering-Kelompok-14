<x-app-layout>
    <x-slot name="title">Dashboard - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">Welcome back, {{ auth()->user()->name }}!</h2>
                                <p class="text-muted mb-0">Continue your learning journey and achieve your goals.</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="avatar avatar-lg">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-book display-6 mb-2"></i>
                        <h3>{{ $enrolledCourses->count() }}</h3>
                        <small>Enrolled Courses</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle display-6 mb-2"></i>
                        <h3>{{ $enrolledCourses->filter(function($course) { return optional($course->pivot)->is_completed; })->count() }}</h3>
                        <small>Completed</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-question-circle display-6 mb-2"></i>
                        <h3>{{ $recentHomework->count() }}</h3>
                        <small>Homework Questions</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-trophy display-6 mb-2"></i>
                        <h3>{{ auth()->user()->points }}</h3>
                        <small>Points Earned</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Courses</h5>
                        <a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus me-1"></i>Browse More
                        </a>
                    </div>
                    <div class="card-body">
                        @if($enrolledCourses->count() > 0)
                            @foreach($enrolledCourses as $course)
                                @php
                                    $courseProgress = \App\Models\UserProgress::where('user_id', auth()->id())
                                                                              ->where('course_id', $course->id)
                                                                              ->whereNull('module_id')
                                                                              ->first();
                                    $progressPercentage = $courseProgress ? $courseProgress->progress_percentage : 0;
                                @endphp
                                <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $course->title }}</h6>
                                        <small class="text-muted">by {{ $course->instructor->name }}</small>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar" style="width: {{ $progressPercentage }}%"></div>
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        <span class="badge bg-light text-dark mb-1">{{ round($progressPercentage) }}%</span>
                                        <div>
                                            @if($progressPercentage >= 100)
                                                <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-sm btn-success">View</a>
                                            @else
                                                <a href="{{ route('courses.learn', $course->slug) }}" class="btn btn-sm btn-primary">
                                                    @if($progressPercentage > 0)
                                                        Continue
                                                    @else
                                                        Start
                                                    @endif
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-book display-4 text-muted"></i>
                                <h6 class="text-muted mt-3">No enrolled courses yet</h6>
                                <p class="text-muted">Explore our course catalog to start learning!</p>
                                <a href="{{ route('courses.index') }}" class="btn btn-primary">Browse Courses</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Homework</h5>
                        <a href="{{ route('homework.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        @if($recentHomework->count() > 0)
                            @foreach($recentHomework as $homework)
                                <div class="d-flex align-items-start mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ Str::limit($homework->title, 30) }}</h6>
                                        <small class="text-muted">{{ $homework->course->title }}</small>
                                        <div class="mt-1">
                                            <span class="badge bg-{{ $homework->status === 'pending' ? 'warning' : ($homework->status === 'answered' ? 'success' : 'secondary') }}">
                                                {{ ucfirst($homework->status) }}
                                            </span>
                                            <span class="badge bg-info">{{ ucfirst($homework->difficulty) }}</span>
                                        </div>
                                    </div>
                                    <small class="text-muted">{{ $homework->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-question-circle display-4 text-muted"></i>
                                <h6 class="text-muted mt-3">No homework yet</h6>
                                <a href="{{ route('homework.create') }}" class="btn btn-sm btn-primary">Ask Question</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('homework.create') }}" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle me-2"></i>Ask Homework Question
                            </a>
                            <a href="{{ route('forum.create') }}" class="btn btn-outline-success">
                                <i class="bi bi-chat-left-text me-2"></i>Start Discussion
                            </a>
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-info">
                                <i class="bi bi-gear me-2"></i>Update Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>