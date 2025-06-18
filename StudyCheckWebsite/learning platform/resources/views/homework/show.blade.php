<x-app-layout>
    <x-slot name="title">{{ $homework->title }} - Homework - {{ config('app.name') }}</x-slot>

    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h4 class="fw-bold mb-2">{{ $homework->title }}</h4>
                                <div class="d-flex gap-2">
                                    <span class="badge text-bg-{{ $homework->status === 'pending' ? 'warning' : ($homework->status === 'answered' ? 'success' : 'secondary') }}">{{ ucfirst($homework->status) }}</span>
                                    <span class="badge text-bg-primary">{{ ucfirst($homework->subject) }}</span>
                                    <span class="badge text-bg-info">{{ ucfirst($homework->difficulty) }}</span>
                                </div>
                            </div>
                            @if(auth()->id() === $homework->student_id)
                                <div class="dropdown" style="position: relative; z-index: 2;">
                                    <button class="btn btn-light btn-sm" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ route('homework.edit', $homework->id) }}"><i class="bi bi-pencil-fill me-2"></i>Edit Question</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <button class="dropdown-item text-danger" onclick="if(confirm('Are you sure?')) document.getElementById('delete-form').submit();">
                                                <i class="bi bi-trash-fill me-2"></i>Delete Question
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                                <form id="delete-form" action="{{ route('homework.destroy', $homework->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ $homework->student->avatar_url }}" alt="{{ $homework->student->name }}" class="avatar me-3">
                            <div>
                                <div class="fw-bold d-flex align-items-center gap-1">
                                    {{ $homework->student->name }}
                                    @if($homework->student->is_verified)
                                        <i class="bi bi-patch-check-fill text-primary" title="Verified"></i>
                                    @endif
                                </div>
                                <small class="text-muted">Asked in <a href="{{ route('courses.show', $homework->course->slug) }}" class="text-decoration-none fw-semibold">{{ $homework->course->title }}</a> • {{ $homework->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-semibold">Description:</h6>
                            <p class="text-muted">{{ $homework->description }}</p>
                        </div>

                        <div class="mb-3">
                            <h6 class="fw-semibold">Question:</h6>
                            <div class="p-3 bg-light rounded fs-5 lh-lg">
                                {!! nl2br(e($homework->question)) !!}
                            </div>
                        </div>

                        @if($homework->attachments && count($homework->attachments) > 0)
                            <div class="mb-3">
                                <h6 class="fw-semibold">Attachments:</h6>
                                <div class="list-group">
                                    @foreach($homework->attachments as $attachment)
                                        <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="bi bi-file-earmark-arrow-down-fill me-2"></i> {{ $attachment['name'] }}
                                                <small class="text-muted ms-2">({{ number_format($attachment['size'] / 1024, 1) }} KB)</small>
                                            </div>
                                            <i class="bi bi-download"></i>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">{{ $answers->count() }} Answer{{ $answers->count() !== 1 ? 's' : '' }}</h5>
                    </div>
                    <div class="card-body p-4">
                        @forelse($answers as $answer)
                            <div class="d-flex mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <img src="{{ $answer->teacher->avatar_url }}" alt="{{ $answer->teacher->name }}" class="avatar me-3 mt-1">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span class="fw-bold d-inline-flex align-items-center gap-1">
                                                {{ $answer->teacher->name }}
                                                @if($answer->teacher->is_verified)
                                                    <i class="bi bi-patch-check-fill text-primary" title="Verified"></i>
                                                @endif
                                            </span>
                                            <span class="badge bg-info-subtle text-info-emphasis rounded-pill ms-2">{{ ucfirst($answer->teacher->role) }}</span>
                                        </div>
                                        @if($answer->is_best_answer)
                                            <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Best Answer</span>
                                        @endif
                                    </div>
                                    <div class="mb-3 p-3 bg-light rounded">{!! nl2br(e($answer->answer)) !!}</div>

                                    @if($answer->explanation)
                                    <div class="mb-3">
                                        <strong class="small d-block mb-1">Explanation:</strong>
                                        <div class="p-3 border-start border-4 border-info bg-info-subtle rounded small">
                                            {!! nl2br(e($answer->explanation)) !!}
                                        </div>
                                    </div>
                                    @endif

                                    <div class="d-flex align-items-center gap-2">
                                        @auth
                                            @if(auth()->id() === $homework->student_id && !$answer->is_best_answer)
                                                <button class="btn btn-sm btn-outline-success" onclick="markBestAnswer({{ $answer->id }})"><i class="bi bi-check-lg"></i> Mark as Best</button>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">No answers yet. Be the first to help!</p>
                        @endforelse
                    </div>
                </div>

                @auth
                    @if(in_array(auth()->user()->role, ['teacher', 'expert', 'admin']) && $homework->status !== 'closed')
                        <div class="card mt-4 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="fw-bold mb-0">Your Answer</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('homework.answer', $homework->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="answer" class="form-label fw-semibold">Answer</label>
                                        <textarea name="answer" class="form-control" rows="5" placeholder="Provide a clear and helpful answer..." required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="explanation" class="form-label fw-semibold">Explanation (Optional)</label>
                                        <textarea name="explanation" class="form-control" rows="3" placeholder="Explain the steps or reasoning..."></textarea>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill me-2"></i>Submit Answer</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">Question Details</h6>
                    </div>
                    <div class="card-body">
                         <ul class="list-unstyled d-grid gap-3">
                            <li><strong class="d-block">Course</strong><a href="{{ route('courses.show', $homework->course->slug) }}" class="text-decoration-none">{{ $homework->course->title }}</a></li>
                            <li><strong class="d-block">Asked by</strong><a href="{{ route('profile.show', $homework->student_id) }}" class="text-decoration-none">{{ $homework->student->name }}</a></li>
                            @if($homework->due_date)
                                <li><strong class="d-block">Due Date</strong>{{ $homework->due_date->format('M d, Y') }} @if($homework->due_date->isPast())<span class="badge bg-danger ms-2">Overdue</span>@endif</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
        @if(auth()->check() && auth()->id() === $homework->student_id)
            <script>
                function markBestAnswer(answerId) {
                    if (confirm('Mark this as the best answer? This will close the question.')) {
                        fetch(`{{ url('/homework/answer') }}/${answerId}/best-answer`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(response => response.json()).then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        }).catch(error => console.error('Error:', error));
                    }
                }
            </script>
        @endif
    @endpush
</x-app-layout>