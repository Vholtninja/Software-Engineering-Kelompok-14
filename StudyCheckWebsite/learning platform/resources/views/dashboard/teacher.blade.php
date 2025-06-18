<!-- resources/views/dashboard/teacher.blade.php -->
<x-app-layout>
    <x-slot name="title">Teacher Dashboard - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="mb-2">Welcome, {{ auth()->user()->name }}!</h2>
                                <p class="text-muted mb-0">Manage your courses and help students succeed.</p>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="avatar avatar-lg">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-book display-6 mb-2"></i>
                        <h3>{{ $myCourses->count() }}</h3>
                        <small>My Courses</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-people display-6 mb-2"></i>
                        <h3>{{ $myCourses->sum('students_count') }}</h3>
                        <small>Total Students</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-question-circle display-6 mb-2"></i>
                        <h3>{{ $pendingHomework }}</h3>
                        <small>Pending Homework</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="bi bi-star display-6 mb-2"></i>
                        <h3>{{ auth()->user()->points }}</h3>
                        <small>Reputation</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- My Courses -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">My Courses</h5>
                        <a href="{{ route('courses.create') }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus me-1"></i>Create Course
                        </a>
                    </div>
                    <div class="card-body">
                        @if($myCourses->count() > 0)
                            @foreach($myCourses as $course)
                                <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $course->title }}</h6>
                                        <small class="text-muted">{{ $course->category }} • {{ ucfirst($course->level) }}</small>
                                        <div class="mt-1">
                                            <span class="badge bg-{{ $course->is_active ? 'success' : 'secondary' }}">
                                                {{ $course->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            <span class="badge bg-info">{{ $course->students_count }} students</span>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-outline-primary">View</a>
                                            <a href="{{ route('courses.edit', $course->slug) }}" class="btn btn-outline-secondary">Edit</a>
                                            <a href="{{ route('modules.create', $course->slug) }}" class="btn btn-outline-success">Add Module</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-book display-4 text-muted"></i>
                                <h6 class="text-muted mt-3">No courses created yet</h6>
                                <p class="text-muted">Create your first course to start teaching!</p>
                                <a href="{{ route('courses.create') }}" class="btn btn-primary">Create Course</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions & Homework -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('courses.create') }}" class="btn btn-outline-primary">
                                <i class="bi bi-plus-circle me-2"></i>Create Course
                            </a>
                            <a href="{{ route('homework.index') }}" class="btn btn-outline-warning">
                                <i class="bi bi-question-circle me-2"></i>Answer Homework
                            </a>
                            <a href="{{ route('forum.index') }}" class="btn btn-outline-success">
                                <i class="bi bi-chat-left-text me-2"></i>Forum Discussions
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pending Homework -->
                @if($pendingHomework > 0)
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Pending Homework</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                You have <strong>{{ $pendingHomework }}</strong> homework questions waiting for your answer.
                            </div>
                            <a href="{{ route('homework.index', ['status' => 'pending']) }}" class="btn btn-warning">
                                <i class="bi bi-arrow-right me-2"></i>Answer Questions
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>