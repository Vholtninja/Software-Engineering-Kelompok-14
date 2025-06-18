<x-app-layout>
    <x-slot name="title">{{ $course->title }} - {{ config('app.name') }}</x-slot>

    @push('styles')
    <style>
        .course-header {
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('{{ $course->thumbnail_url }}');
            background-size: cover;
            background-position: center;
        }
        .module-item .module-icon {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bs-light);
            border: 2px solid var(--bs-primary);
            color: var(--bs-primary);
            font-weight: 700;
        }
        .module-item.completed .module-icon {
             background-color: var(--bs-success);
             border-color: var(--bs-success);
             color: #fff;
        }
        .management-panel .list-group-item {
            font-weight: 600;
        }
    </style>
    @endpush

    <div class="course-header text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <span class="badge bg-light text-dark mb-2 fs-6">{{ ucfirst($course->category) }}</span>
                    <h1 class="display-5 fw-bold">{{ $course->title }}</h1>
                    <p class="lead">{{ $course->description }}</p>
                    <div class="d-flex align-items-center mt-4">
                        <img src="{{ $course->instructor->avatar_url }}" alt="{{ $course->instructor->name }}" class="avatar me-3 border border-2 border-white">
                        <div>
                            <div class="fw-bold">{{ $course->instructor->name }}</div>
                            <small>{{ ucfirst($course->instructor->role) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-n5">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="fw-bold mb-4">What you'll learn</h4>
                        <div class="row g-3">
                            <div class="col-md-4 text-center">
                                <i class="bi bi-journal-text fs-2 text-primary"></i>
                                <div class="fw-bold mt-2">{{ $course->modules->count() }} Modules</div>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="bi bi-clock-history fs-2 text-primary"></i>
                                <div class="fw-bold mt-2">{{ $course->duration_minutes }}m Duration</div>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="bi bi-people-fill fs-2 text-primary"></i>
                                <div class="fw-bold mt-2">{{ $studentCount }} Students</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="fw-bold mb-0">Course Content</h4>
                    </div>
                    <div class="card-body p-0">
                        @if($course->modules->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($course->modules as $module)
                                    <div class="list-group-item p-3 module-item {{ auth()->check() && $module->isCompletedByUser(auth()->id()) ? 'completed' : '' }}">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle module-icon me-3">
                                                @if(auth()->check() && $module->isCompletedByUser(auth()->id()))
                                                    <i class="bi bi-check-lg"></i>
                                                @else
                                                    {{ $loop->iteration }}
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold">{{ $module->title }}</h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>{{ $module->duration_minutes }}m
                                                    @if($module->quizzes->count() > 0)
                                                        <span class="mx-2">•</span> <i class="bi bi-patch-question-fill"></i> Quiz
                                                    @endif
                                                </small>
                                            </div>
                                            @if($isEnrolled)
                                                <i class="bi bi-play-circle-fill fs-4 text-primary"></i>
                                            @else
                                                <i class="bi bi-lock-fill text-muted"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-5">
                                <p class="text-muted">Course content will be added soon.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-body p-4">
                        <h2 class="fw-bold mb-3">{{ $course->formatted_price }}</h2>
                        
                        @auth
                            @if(auth()->id() === $course->instructor_id)
                                <div class="alert alert-info text-center">This is your course.</div>
                                <a href="#management-panel" class="btn btn-info w-100">
                                    <i class="bi bi-sliders me-2"></i>Manage Your Course
                                </a>
                            @elseif($isEnrolled)
                                <a href="{{ route('courses.learn', $course->slug) }}" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-play-circle-fill me-2"></i>
                                    {{ $progress && $progress->progress_percentage > 0 ? 'Continue Learning' : 'Start Learning' }}
                                </a>
                                @if($progress)
                                    <div class="mt-3">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress_percentage }}%" aria-valuenow="{{ $progress->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">{{ round($progress->progress_percentage) }}% completed</small>
                                    </div>
                                @endif
                            @else
                                <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        {{ $course->price > 0 ? 'Enroll Now' : 'Enroll For Free' }}
                                    </button>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">Sign In to Enroll</a>
                        @endauth

                        <hr>
                        <h6 class="fw-bold">This course includes:</h6>
                        <ul class="list-unstyled d-grid gap-2 mt-3">
                            <li><i class="bi bi-camera-video-fill me-2 text-primary"></i> On-demand video</li>
                            <li><i class="bi bi-file-earmark-arrow-down-fill me-2 text-primary"></i> Downloadable resources</li>
                            <li><i class="bi bi-infinity me-2 text-primary"></i> Full lifetime access</li>
                            <li><i class="bi bi-phone-fill me-2 text-primary"></i> Access on mobile and desktop</li>
                            <li><i class="bi bi-patch-check-fill me-2 text-primary"></i> Certificate of completion</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->check() && (auth()->id() === $course->instructor_id || auth()->user()->isAdmin()))
        <div id="management-panel" class="row mt-5">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0"><i class="bi bi-sliders me-2"></i> Course Management Panel</h4>
                        <div class="dropdown">
                            <button class="btn btn-danger dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-gear-fill me-2"></i> Actions
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('courses.edit', $course->slug) }}">
                                        <i class="bi bi-pencil-square me-2"></i>Edit Course Details
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger" onclick="deleteCourse()">
                                        <i class="bi bi-trash-fill me-2"></i>Delete This Course
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body p-4">
                       <div class="row g-4">
                           <div class="{{ $course->modules->isNotEmpty() ? 'col-md-6' : 'col-md-12' }}">
                               <h5 class="fw-bold">Modules</h5>
                               <div class="list-group">
                                    @forelse($course->modules as $module)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>{{$loop->iteration}}. {{ $module->title }}</span>
                                            <div class="btn-group">
                                                <a href="{{ route('modules.show', [$course->slug, $module->id]) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                                <a href="{{ route('modules.edit', [$course->slug, $module->id]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteModule({{ $module->id }})"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted">No modules yet. Add the first module to get started.</p>
                                    @endforelse
                               </div>
                               <a href="{{ route('modules.create', $course->slug) }}" class="btn btn-primary mt-3"><i class="bi bi-plus-lg me-2"></i>Add Module</a>
                           </div>

                           @if($course->modules->isNotEmpty())
                           <div class="col-md-6">
                               <h5 class="fw-bold">Final Project</h5>
                                @if($course->finalProjects->count() > 0)
                                    @foreach($course->finalProjects as $project)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>{{ $project->title }}</span>
                                        <div class="btn-group">
                                            <a href="{{ route('final-projects.show', [$course->slug, $project->id]) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('final-projects.submissions', [$course->slug, $project->id]) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-card-checklist"></i></a>
                                            <a href="{{ route('final-projects.edit', [$course->slug, $project->id]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">No final project assigned.</p>
                                    <a href="{{ route('final-projects.create', $course->slug) }}" class="btn btn-info mt-2"><i class="bi bi-plus-lg me-2"></i>Add Final Project</a>
                                @endif
                           </div>
                           @endif
                       </div>
                    </div>
                </div>
            </div>
        </div>
        
        <form id="delete-course-form" action="{{ route('courses.destroy', $course->slug) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>

        @foreach($course->modules as $module)
             <form id="delete-module-form-{{ $module->id }}" action="{{ route('modules.destroy', [$course->slug, $module->id]) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        @endif
    </div>

    @push('scripts')
        <script>
            function deleteCourse() {
                if (confirm('Are you sure you want to delete this entire course? This action cannot be undone.')) {
                    document.getElementById('delete-course-form').submit();
                }
            }
            function deleteModule(moduleId) {
                if (confirm('Are you sure you want to delete this module? This action will also delete all associated quizzes and cannot be undone.')) {
                    document.getElementById(`delete-module-form-${moduleId}`).submit();
                }
            }
        </script>
    @endpush
</x-app-layout>