<x-app-layout>
    <x-slot name="title">Taking: {{ $quiz->title }} - {{ config('app.name') }}</x-slot>

    @push('styles')
    <style>
        .quiz-timer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1056;
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid var(--bs-primary);
            border-radius: 10px;
            padding: 10px 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .quiz-timer.warning {
            border-color: #ffc107;
            animation: pulse-warning 1.5s infinite;
        }
        .quiz-timer.danger {
            border-color: #dc3545;
            animation: pulse-danger 0.8s infinite;
        }
        @keyframes pulse-warning {
            0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
        }
        @keyframes pulse-danger {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
    </style>
    @endpush

    <div class="quiz-timer" id="timer">
        <div class="text-center">
            <i class="bi bi-clock-history"></i>
            <div class="fw-bold fs-5" id="time-remaining">{{ $quiz->time_limit }}:00</div>
            <small>Time Left</small>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h4 class="mb-0 fw-bold">{{ $quiz->title }}</h4>
                        <div id="question-progress" class="fw-semibold text-muted"></div>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <form id="quiz-form" action="{{ route('quizzes.submit', [$quiz->module->course->slug, $quiz->module->id, $quiz->id]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="started_at" value="{{ now()->toISOString() }}">

                            @foreach($questions as $index => $question)
                                <div class="question-slide" id="question-{{ $index }}" style="display: none;">
                                    <div class="mb-4">
                                        <h5 class="fw-bold">{{ $loop->iteration }}. {{ $question->question }}</h5>
                                        <span class="badge bg-primary rounded-pill">{{ $question->points }} Points</span>
                                    </div>
                                    
                                    @if($question->type === 'multiple_choice')
                                        <div class="list-group">
                                        @foreach($question->options as $optionIndex => $option)
                                            <label class="list-group-item list-group-item-action">
                                                <input class="form-check-input me-2" type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" required>
                                                {{ $option }}
                                            </label>
                                        @endforeach
                                        </div>
                                    @elseif($question->type === 'true_false')
                                        <div class="list-group">
                                            <label class="list-group-item list-group-item-action">
                                                <input class="form-check-input me-2" type="radio" name="answers[{{ $question->id }}]" value="True" required> True
                                            </label>
                                            <label class="list-group-item list-group-item-action">
                                                <input class="form-check-input me-2" type="radio" name="answers[{{ $question->id }}]" value="False" required> False
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            
                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-secondary" id="prev-btn">
                                    <i class="bi bi-arrow-left me-2"></i>Previous
                                </button>
                                <button type="button" class="btn btn-primary" id="next-btn">
                                    Next<i class="bi bi-arrow-right ms-2"></i>
                                </button>
                                <button type="submit" class="btn btn-success" id="submit-btn" style="display: none;">
                                    <i class="bi bi-check-circle-fill me-2"></i>Submit Quiz
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questions = document.querySelectorAll('.question-slide');
            const nextBtn = document.getElementById('next-btn');
            const prevBtn = document.getElementById('prev-btn');
            const submitBtn = document.getElementById('submit-btn');
            const progressText = document.getElementById('question-progress');
            let currentQuestionIndex = 0;

            function showQuestion(index) {
                questions.forEach((question, i) => {
                    question.style.display = i === index ? 'block' : 'none';
                });
                progressText.textContent = `Question ${index + 1} / ${questions.length}`;
                prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
                nextBtn.style.display = index === questions.length - 1 ? 'none' : 'inline-block';
                submitBtn.style.display = index === questions.length - 1 ? 'inline-block' : 'none';
            }

            nextBtn.addEventListener('click', () => {
                if (currentQuestionIndex < questions.length - 1) {
                    currentQuestionIndex++;
                    showQuestion(currentQuestionIndex);
                }
            });

            prevBtn.addEventListener('click', () => {
                if (currentQuestionIndex > 0) {
                    currentQuestionIndex--;
                    showQuestion(currentQuestionIndex);
                }
            });

            submitBtn.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to submit the quiz? You cannot change your answers after submission.')) {
                    e.preventDefault();
                } else {
                    clearInterval(countdown);
                }
            });

            showQuestion(0);

            const timerEl = document.getElementById('timer');
            const timeDisplay = document.getElementById('time-remaining');
            let timeLimit = {{ $quiz->time_limit * 60 }};
            
            const countdown = setInterval(() => {
                timeLimit--;
                const minutes = Math.floor(timeLimit / 60);
                const seconds = timeLimit % 60;
                timeDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                timerEl.classList.remove('warning', 'danger');
                if (timeLimit <= 60) {
                    timerEl.classList.add('danger');
                } else if (timeLimit <= 300) {
                    timerEl.classList.add('warning');
                }

                if (timeLimit <= 0) {
                    clearInterval(countdown);
                    alert('Time is up! Your quiz will be submitted automatically.');
                    document.getElementById('quiz-form').submit();
                }
            }, 1000);
        });

        window.addEventListener('beforeunload', function(e) {
            e.preventDefault();
            e.returnValue = 'Are you sure you want to leave? Your quiz progress might be lost.';
            return e.returnValue;
        });

        document.getElementById('quiz-form').addEventListener('submit', function() {
            window.onbeforeunload = null;
        });
    </script>
    @endpush
</x-app-layout>