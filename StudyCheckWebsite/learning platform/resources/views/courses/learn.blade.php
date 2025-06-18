<x-app-layout>
    <x-slot name="title">Learning: {{ $course->title }} - {{ config('app.name') }}</x-slot>

    @push('styles')
    <style>
        .learning-container {
            height: calc(100vh - 76px);
            overflow: hidden;
        }
        .module-sidebar {
            height: 100%;
            overflow-y: auto;
            border-right: 1px solid #dee2e6;
            background-color: #fff;
        }
        .content-area {
            height: 100%;
            overflow-y: auto;
        }
        .module-item {
            cursor: pointer;
            transition: all 0.2s ease;
            border-left: 4px solid transparent;
        }
        .module-item:hover {
            background-color: #f8f9fa;
        }
        .module-item.active {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
            border-left-color: var(--bs-primary);
            font-weight: 600;
        }
        .video-container {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            background-color: #000;
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        @media (max-width: 991px) {
            .learning-container {
                height: auto;
            }
            .module-sidebar {
                height: auto;
                max-height: 50vh;
                border-right: none;
                border-bottom: 1px solid #dee2e6;
            }
        }
    </style>
    @endpush

    <div class="learning-container">
        <div class="row g-0 h-100">
            <div class="col-lg-3 module-sidebar">
                <div class="p-3 border-bottom">
                    <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none text-dark fw-bold mb-3 d-block"><i class="bi bi-arrow-left me-2"></i>{{ $course->title }}</a>
                    <div class="progress mb-1" style="height: 8px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $progress->progress_percentage ?? 0 }}%" aria-valuenow="{{ $progress->progress_percentage ?? 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted">{{ round($progress->progress_percentage ?? 0) }}% Complete</small>
                </div>

                <div class="list-group list-group-flush">
                    @foreach($course->modules as $module)
                        <div class="list-group-item list-group-item-action module-item p-3 {{ $currentModule->id === $module->id ? 'active' : '' }}" onclick="loadModule({{ $module->id }})">
                            <div class="d-flex align-items-center">
                                 @if($module->isCompletedByUser(auth()->id()))
                                    <i class="bi bi-check-circle-fill text-success me-3 fs-5"></i>
                                @else
                                    <i class="bi bi-circle me-3 fs-5 text-muted"></i>
                                @endif
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-semibold">{{ $module->title }}</p>
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>{{ $module->duration_minutes }}m
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-9 content-area">
                @if($currentModule)
                <div class="p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold">{{ $currentModule->title }}</h2>
                            @if($currentModule->description)
                                <p class="text-muted fs-5">{{ $currentModule->description }}</p>
                            @endif
                        </div>
                        <div>
                            @if(!$currentModule->isCompletedByUser(auth()->id()))
                                <button class="btn btn-success" onclick="markAsComplete()">
                                    <i class="bi bi-check-circle me-2"></i>Mark as Complete
                                </button>
                            @else
                                <span class="badge bg-success fs-6 rounded-pill"><i class="bi bi-check-circle-fill me-1"></i> Completed</span>
                            @endif
                        </div>
                    </div>

                    @if($currentModule->video_url)
                        <div class="video-container mb-4 shadow-lg">
                             @if(strpos($currentModule->video_url, 'youtube.com') !== false || strpos($currentModule->video_url, 'youtu.be') !== false)
                                @php
                                    $videoId = '';
                                    if (strpos($currentModule->video_url, 'watch?v=') !== false) {
                                        parse_str(parse_url($currentModule->video_url, PHP_URL_QUERY), $params);
                                        $videoId = $params['v'] ?? '';
                                    } elseif (strpos($currentModule->video_url, 'youtu.be') !== false) {
                                        $videoId = basename(parse_url($currentModule->video_url, PHP_URL_PATH));
                                    }
                                @endphp
                                <iframe src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            @else
                                 <video controls class="w-100 h-100 position-absolute top-0 start-0">
                                    <source src="{{ $currentModule->video_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                    @endif

                    <div class="card shadow-sm">
                         <div class="card-body p-4 fs-5 lh-lg">
                            {!! nl2br(e($currentModule->content)) !!}
                        </div>
                    </div>

                    @if($currentModule->attachments && count($currentModule->attachments) > 0)
                         <div class="card mt-4 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h6 class="fw-bold mb-0"><i class="bi bi-paperclip me-2"></i>Attachments</h6>
                            </div>
                             <div class="list-group list-group-flush">
                                @foreach($currentModule->attachments as $attachment)
                                     <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-file-earmark-arrow-down-fill me-2"></i>
                                            <span class="fw-semibold">{{ $attachment['name'] }}</span>
                                            <small class="text-muted ms-2">({{ number_format($attachment['size'] / 1024, 1) }} KB)</small>
                                        </div>
                                        <i class="bi bi-download"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($currentModule->quizzes->count() > 0)
                        <div class="card mt-4 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h6 class="fw-bold mb-0"><i class="bi bi-patch-question-fill me-2"></i>Quiz</h6>
                            </div>
                            <div class="card-body">
                                @foreach($currentModule->quizzes as $quiz)
                                    <h5 class="fw-bold">{{ $quiz->title }}</h5>
                                    @if($quiz->description)
                                        <p class="text-muted">{{ $quiz->description }}</p>
                                    @endif
                                    
                                    @php
                                        $hasAttempts = $quiz->quizAttempts->isNotEmpty();
                                    @endphp
                                    <a href="{{ route('quizzes.show', [$course->slug, $currentModule->id, $quiz->id]) }}" class="btn {{ $hasAttempts ? 'btn-info' : 'btn-outline-primary' }}">
                                        @if($hasAttempts)
                                            <i class="bi bi-eye-fill me-1"></i> View Result
                                        @else
                                            <i class="bi bi-play-circle-fill me-1"></i> Start Quiz
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    @if($course->finalProjects->count() > 0)
                        <div class="card mt-4 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h6 class="fw-bold mb-0"><i class="bi bi-mortarboard-fill me-2"></i>Final Project</h6>
                            </div>
                            <div class="card-body">
                                @foreach($course->finalProjects as $project)
                                    <div class="mb-3">
                                        <h5 class="fw-bold">{{ $project->title }}</h5>
                                        <p class="text-muted small">{{ Str::limit($project->description, 100) }}</p>
                                        <p class="text-muted small mb-1">Deadline: {{ $project->deadline->format('M d, Y H:i') }}</p>

                                        @php
                                            $allModulesCompleted = $progress->progress_percentage >= 100;
                                        @endphp

                                        @if($allModulesCompleted)
                                            <a href="{{ route('final-projects.show', [$course->slug, $project->id]) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-folder-check me-1"></i>View Project & Submit
                                            </a>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled>
                                                <i class="bi bi-lock-fill me-1"></i>Complete All Modules to Unlock
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mt-5">
                        @php
                            $moduleIndex = $course->modules->search(fn($module) => $module->id === $currentModule->id);
                            $prevModule = $moduleIndex > 0 ? $course->modules[$moduleIndex - 1] : null;
                            $nextModule = $moduleIndex < $course->modules->count() - 1 ? $course->modules[$moduleIndex + 1] : null;
                        @endphp

                        @if($prevModule)
                            <a href="{{ route('courses.learn', [$course->slug, 'module' => $prevModule->id]) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Previous
                            </a>
                        @else
                            <div></div>
                        @endif

                        @if($nextModule)
                             @if($currentModule->isCompletedByUser(auth()->id()))
                                <a href="{{ route('courses.learn', [$course->slug, 'module' => $nextModule->id]) }}" class="btn btn-primary">
                                    Next <i class="bi bi-arrow-right ms-2"></i>
                                </a>
                            @else
                                <button class="btn btn-primary" disabled>Next <i class="bi bi-arrow-right ms-2"></i></button>
                            @endif
                        @else
                             <div class="text-end">
                                <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-success"><i class="bi bi-trophy-fill me-2"></i>Finish Course</a>
                            </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let startTime = Date.now();
        function loadModule(moduleId) {
            window.location.href = `{{ route('courses.learn', $course->slug) }}?module=${moduleId}`;
        }

        function markAsComplete() {
            const timeSpent = Math.floor((Date.now() - startTime) / 60000);
            fetch(`{{ route('courses.complete-module', [$course->id, $currentModule->id]) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                 body: JSON.stringify({
                    time_spent: timeSpent
                })
            })
            .then(response => response.json())
            .then(data => {
                 if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to mark as complete. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }
    </script>
    @endpush
</x-app-layout>