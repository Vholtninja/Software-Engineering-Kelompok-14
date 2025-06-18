<x-app-layout>
    <x-slot name="title">{{ $quiz->title }} - Quiz - {{ config('app.name') }}</x-slot>

    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0">{{ $quiz->title }}</h4>
                        @if(auth()->check() && (auth()->id() === $quiz->module->course->instructor_id || auth()->user()->isAdmin()))
                            <div class="btn-group">
                                <a href="{{ route('questions.create', [$quiz->module->course->slug, $quiz->module->id, $quiz->id]) }}" class="btn btn-sm btn-primary">Add Question</a>
                                <a href="{{ route('quizzes.edit', [$quiz->module->course->slug, $quiz->module->id, $quiz->id]) }}" class="btn btn-sm btn-outline-secondary">Edit Quiz</a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        @if($quiz->description)
                            <p class="text-muted fs-5">{{ $quiz->description }}</p>
                            <hr>
                        @endif

                        <div class="row text-center g-3 mb-4">
                            <div class="col-md-3 col-6">
                                <i class="bi bi-clock-fill fs-2 text-primary"></i>
                                <div class="fw-bold mt-1">{{ $quiz->time_limit }}</div>
                                <small class="text-muted">Minutes</small>
                            </div>
                            <div class="col-md-3 col-6">
                                <i class="bi bi-star-fill fs-2 text-warning"></i>
                                <div class="fw-bold mt-1">{{ $quiz->passing_score }}%</div>
                                <small class="text-muted">To Pass</small>
                            </div>
                            <div class="col-md-3 col-6">
                                <i class="bi bi-arrow-repeat fs-2 text-info"></i>
                                <div class="fw-bold mt-1">{{ $quiz->max_attempts }}</div>
                                <small class="text-muted">Max Attempts</small>
                            </div>
                             <div class="col-md-3 col-6">
                                <i class="bi bi-question-circle-fill fs-2 text-success"></i>
                                <div class="fw-bold mt-1">{{ $quiz->questions->count() }}</div>
                                <small class="text-muted">Questions</small>
                            </div>
                        </div>

                        @if(auth()->check() && auth()->id() !== $quiz->module->course->instructor_id)
                            @if($quiz->questions->count() > 0)
                                <div id="quiz-action-container" class="text-center p-4 rounded-3" style="background-color: #f8f9fa;">
                                    @if($lastAttempt && $lastAttempt->passed)
                                        <div class="alert alert-success">
                                            <h4 class="alert-heading fw-bold">Congratulations!</h4>
                                            You have passed this quiz with a score of **{{ $lastAttempt->percentage }}%** and earned **{{ $lastAttempt->points_earned }}** points.
                                        </div>
                                        @if($nextModule)
                                            <a href="{{ route('courses.learn', [$quiz->module->course->slug, 'module' => $nextModule->id]) }}" class="btn btn-success btn-lg">
                                                <i class="bi bi-arrow-right-circle-fill me-2"></i>Go to Next Module
                                            </a>
                                        @else
                                            <a href="{{ route('courses.show', $quiz->module->course->slug) }}" class="btn btn-info btn-lg">
                                                <i class="bi bi-trophy-fill me-2"></i>Course Complete! Back to Course
                                            </a>
                                        @endif
                                    @elseif($userAttempts->count() >= $quiz->max_attempts)
                                        <div class="alert alert-danger">
                                            <h4 class="alert-heading fw-bold">Maximum Attempts Reached</h4>
                                            You can no longer take this quiz.
                                        </div>
                                    @else
                                        @if($lastAttempt)
                                            <p class="text-muted">Your last score was **{{ $lastAttempt->percentage }}%** and you earned **{{ $lastAttempt->points_earned }}** points. You have **{{ $quiz->max_attempts - $userAttempts->count() }}** attempt(s) remaining.</p>
                                        @endif
                                        
                                        <div id="retake-quiz-button" style="{{ ($waitTime && now()->lt($waitTime)) ? 'display: none;' : '' }}">
                                            <a href="{{ route('quizzes.take', [$quiz->module->course->slug, $quiz->module->id, $quiz->id]) }}" class="btn btn-primary btn-lg">
                                                <i class="bi bi-play-circle-fill me-2"></i>{{ $lastAttempt ? 'Retake Quiz' : 'Start Quiz' }}
                                            </a>
                                        </div>
                                        
                                        @if($waitTime && now()->lt($waitTime))
                                            <div id="quiz-countdown" class="alert alert-warning" data-wait-time="{{ $waitTime->toISOString() }}">
                                                <h5 class="alert-heading fw-bold">Please Wait</h5>
                                                <p class="mb-1">You can retake this quiz in:</p>
                                                <p class="countdown-timer fs-2 fw-bold mb-0">--:--</p>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning text-center">This quiz has no questions yet. Please contact your instructor.</div>
                            @endif
                        @elseif(!auth()->check())
                            <div class="text-center">
                                <p class="text-muted">Please sign in to take this quiz.</p>
                                <a href="{{ route('login') }}" class="btn btn-primary">Sign In</a>
                            </div>
                        @endif

                    </div>
                </div>

                @if($userAttempts->count() > 0 && auth()->id() !== $quiz->module->course->instructor_id)
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0">Your Attempts</h5>
                        </div>
                        <div class="list-group list-group-flush">
                            @foreach($userAttempts as $attempt)
                                <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold">Attempt #{{ $loop->count - $loop->index }}</h6>
                                            <small class="text-muted">{{ $attempt->completed_at->format('M d, Y, H:i') }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold fs-5 {{ $attempt->passed ? 'text-success' : 'text-danger' }}">
                                                {{ $attempt->percentage }}% ({{ $attempt->points_earned }} pts)
                                            </div>
                                            <span class="badge bg-{{ $attempt->passed ? 'success' : 'danger' }}">{{ $attempt->passed ? 'Passed' : 'Failed' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                @if(auth()->check() && (auth()->id() === $quiz->module->course->instructor_id || auth()->user()->isAdmin()))
                    <div class="card mt-4">
                         <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0">Quiz Questions ({{ $quiz->questions->count() }})</h5>
                        </div>
                        <div class="list-group list-group-flush">
                             @forelse($quiz->questions as $question)
                                 <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <p class="fw-bold mb-2">{{ $loop->iteration }}. {{ $question->question }} <span class="badge bg-primary rounded-pill">{{ $question->points }} pts</span></p>
                                            @if($question->type === 'multiple_choice')
                                                @foreach($question->options as $option)
                                                    <div class="text-muted {{ $option === $question->correct_answer ? 'text-success fw-bold' : '' }}">
                                                        <i class="bi {{ $option === $question->correct_answer ? 'bi-check-circle-fill' : 'bi-circle' }} me-2"></i>{{ $option }}
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="badge bg-success">Answer: {{ $question->correct_answer }}</span>
                                            @endif
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('questions.edit', [$quiz->module->course->slug, $quiz->module->id, $quiz->id, $question->id]) }}" class="btn btn-outline-secondary">Edit</a>
                                            <button class="btn btn-outline-danger" onclick="deleteQuestion({{ $question->id }})">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                 <div class="card-body text-center p-4">
                                    <p class="text-muted">No questions have been added to this quiz yet.</p>
                                    <a href="{{ route('questions.create', [$quiz->module->course->slug, $quiz->module->id, $quiz->id]) }}" class="btn btn-primary">Add First Question</a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">Course Module</h6>
                    </div>
                    <div class="card-body">
                        <h5 class="fw-bold">{{ $quiz->module->title }}</h5>
                        <p class="text-muted small">{{ $quiz->module->course->title }}</p>
                        <a href="{{ route('courses.learn', [$quiz->module->course->slug, 'module' => $quiz->module->id]) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-arrow-left me-1"></i> Back to Learning
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const countdownElement = document.getElementById('quiz-countdown');
            const retakeButtonContainer = document.getElementById('retake-quiz-button');
            const retakeButton = retakeButtonContainer ? retakeButtonContainer.querySelector('a') : null;

            if (countdownElement && retakeButton) {
                const waitTime = new Date(countdownElement.dataset.waitTime).getTime();

                const interval = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = waitTime - now;

                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    
                    const timerDisplay = countdownElement.querySelector('.countdown-timer');

                    if (distance < 0) {
                        clearInterval(interval);
                        countdownElement.style.display = 'none';
                        retakeButtonContainer.style.display = 'block';
                        if(retakeButton) retakeButton.classList.remove('disabled');
                    } else {
                         if(timerDisplay) timerDisplay.innerHTML = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    }
                }, 1000);
            }
        });

        function deleteQuestion(questionId) {
            if (confirm('Are you sure you want to delete this question?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/courses/{{ $quiz->module->course->slug }}/modules/{{ $quiz->module->id }}/quizzes/{{ $quiz->id }}/questions/${questionId}`;
                form.innerHTML = `@csrf @method('DELETE')`;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    @endpush
</x-app-layout>