<x-app-layout>
    <x-slot name="title">{{ $module->title }} - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ $module->title }}</h4>
                        @if(auth()->check() && (auth()->id() === $module->course->instructor_id || auth()->user()->isAdmin()))
                            <div class="btn-group">
                                <a href="{{ route('modules.edit', [$module->course->slug, $module->id]) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                <a href="{{ route('quizzes.create', [$module->course->slug, $module->id]) }}" class="btn btn-primary btn-sm">Add Quiz</a>
                                @if( !$module->quizzes()->exists() && !$module->course->finalProjects()->exists()) {{-- Hanya tampilkan jika belum ada quiz atau final project --}}
                                <a href="{{ route('final-projects.create', $module->course->slug) }}" class="btn btn-info btn-sm">Add Final Project</a>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($module->description)
                            <p class="text-muted">{{ $module->description }}</p>
                        @endif

                        @if($module->video_url)
                            <div class="video-container mb-4">
                                @if(strpos($module->video_url, 'youtube.com/watch?v=') !== false || strpos($module->video_url, 'youtu.be/') !== false)
                                    @php
                                        $videoId = '';
                                        if (strpos($module->video_url, 'youtube.com/watch?v=') !== false) {
                                            parse_str(parse_url($module->video_url, PHP_URL_QUERY), $params);
                                            $videoId = $params['v'] ?? '';
                                        } elseif (strpos($module->video_url, 'youtu.be/') !== false) {
                                            $videoId = basename(parse_url($module->video_url, PHP_URL_PATH));
                                        }
                                    @endphp
                                    <iframe width="100%" height="400" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>
                                @else
                                    <video controls class="w-100" style="max-height: 400px;">
                                        <source src="{{ $module->video_url }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @endif
                            </div>
                        @endif

                        <div class="content">
                            {!! nl2br(e($module->content)) !!}
                        </div>

                        @if($module->attachments && count($module->attachments) > 0)
                            <div class="mt-4">
                                <h6>Attachments:</h6>
                                <div class="list-group">
                                    @foreach($module->attachments as $attachment)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-file-earmark me-2"></i>
                                                {{ $attachment['name'] }}
                                                <small class="text-muted">({{ number_format($attachment['size'] / 1024, 2) }} KB)</small>
                                            </div>
                                            <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                Download
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($module->quizzes->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Quizzes</h5>
                        </div>
                        <div class="card-body">
                            @foreach($module->quizzes as $quiz)
                                <div class="d-flex justify-content-between align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div>
                                        <h6 class="mb-1">{{ $quiz->title }}</h6>
                                        @if($quiz->description)
                                            <p class="text-muted small mb-1">{{ $quiz->description }}</p>
                                        @endif
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ $quiz->time_limit }} minutes
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-star me-1"></i>{{ $quiz->passing_score }}% to pass
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-arrow-repeat me-1"></i>{{ $quiz->max_attempts }} attempts
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('quizzes.show', [$module->course->slug, $module->id, $quiz->id]) }}" class="btn btn-primary btn-sm">
                                            Take Quiz
                                        </a>
                                        @if(auth()->check() && (auth()->id() === $module->course->instructor_id || auth()->user()->isAdmin()))
                                            <a href="{{ route('quizzes.edit', [$module->course->slug, $module->id, $quiz->id]) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteQuiz('{{ $module->course->slug }}', {{ $module->id }}, {{ $quiz->id }})">Delete</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($module->course->finalProjects()->exists() && $module->id === $module->course->modules->last()->id)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Final Projects</h5>
                        </div>
                        <div class="card-body">
                            @foreach($module->course->finalProjects as $project)
                                <div class="d-flex justify-content-between align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div>
                                        <h6 class="mb-1">{{ $project->title }}</h6>
                                        <p class="text-muted small mb-1">{{ Str::limit($project->description, 100) }}</p>
                                        <small class="text-muted">
                                            Deadline: {{ $project->deadline->format('M d, Y H:i') }}
                                        </small>
                                    </div>
                                    <div class="btn-group">
                                        <a href="{{ route('final-projects.show', [$module->course->slug, $project->id]) }}" class="btn btn-primary btn-sm">
                                            View Project
                                        </a>
                                        @if(auth()->check() && (auth()->id() === $module->course->instructor_id || auth()->user()->isAdmin()))
                                            <a href="{{ route('final-projects.edit', [$module->course->slug, $project->id]) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteFinalProject('{{ $module->course->slug }}', {{ $project->id }})">Delete</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Module Info</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Course:</strong> {{ $module->course->title }}</p>
                        <p><strong>Duration:</strong> {{ $module->duration_minutes }} minutes</p>
                        <p><strong>Order:</strong> {{ $module->order }}</p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-{{ $module->is_published ? 'success' : 'warning' }}">
                                {{ $module->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function deleteQuiz(courseSlug, moduleId, quizId) {
            if (confirm('Are you sure you want to delete this quiz? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/courses/${courseSlug}/modules/${moduleId}/quizzes/${quizId}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteFinalProject(courseSlug, projectId) {
            if (confirm('Are you sure you want to delete this final project? This action cannot be undone.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/courses/${courseSlug}/final-projects/${projectId}`;
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