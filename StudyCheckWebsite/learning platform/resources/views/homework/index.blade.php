<!-- resources/views/homework/index.blade.php -->
<x-app-layout>
    <x-slot name="title">Homework Help - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>Homework Help</h2>
                <p class="text-muted">Get help with your homework from teachers and experts</p>
            </div>
            <div class="col-md-4 text-md-end">
                @if(auth()->user()->role === 'student')
                    <a href="{{ route('homework.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Ask Question
                    </a>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('homework.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="answered" {{ request('status') === 'answered' ? 'selected' : '' }}>Answered</option>
                                <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="subject" class="form-select">
                                <option value="">All Subjects</option>
                                <option value="math" {{ request('subject') === 'math' ? 'selected' : '' }}>Math</option>
                                <option value="science" {{ request('subject') === 'science' ? 'selected' : '' }}>Science</option>
                                <option value="english" {{ request('subject') === 'english' ? 'selected' : '' }}>English</option>
                                <option value="history" {{ request('subject') === 'history' ? 'selected' : '' }}>History</option>
                                <option value="other" {{ request('subject') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="difficulty" class="form-select">
                                <option value="">All Difficulties</option>
                                <option value="easy" {{ request('difficulty') === 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ request('difficulty') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ request('difficulty') === 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Homework List -->
        @if($homework->count() > 0)
            <div class="row">
                @foreach($homework as $hw)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="card-title mb-2">
                                            <a href="{{ route('homework.show', $hw->id) }}" class="text-decoration-none">
                                                {{ $hw->title }}
                                            </a>
                                        </h6>
                                        <p class="card-text text-muted small">{{ Str::limit($hw->description, 100) }}</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $hw->status === 'pending' ? 'warning' : ($hw->status === 'answered' ? 'success' : 'secondary') }}">
                                            {{ ucfirst($hw->status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <img src="{{ $hw->student->avatar_url }}" alt="{{ $hw->student->name }}" class="avatar avatar-sm me-2">
                                    <div class="flex-grow-1">
                                        <small class="text-muted">
                                            by {{ $hw->student->name }} in {{ $hw->course->title }}
                                        </small>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary">{{ ucfirst($hw->subject) }}</span>
                                        <span class="badge bg-info">{{ ucfirst($hw->difficulty) }}</span>
                                        @if($hw->attachments && count($hw->attachments) > 0)
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-paperclip"></i> {{ count($hw->attachments) }}
                                            </span>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $hw->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-chat me-1"></i>{{ $hw->answers->count() }} answers
                                        @if($hw->bestAnswer)
                                            <i class="bi bi-check-circle text-success ms-2"></i>
                                        @endif
                                    </small>
                                    <a href="{{ route('homework.show', $hw->id) }}" class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $homework->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-question-circle display-1 text-muted"></i>
                <h4 class="text-muted mt-3">No homework questions found</h4>
                <p class="text-muted">
                    @if(auth()->user()->role === 'student')
                        Ask your first homework question to get help from teachers!
                    @else
                        No questions need your help right now.
                    @endif
                </p>
                @if(auth()->user()->role === 'student')
                    <a href="{{ route('homework.create') }}" class="btn btn-primary">Ask Question</a>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>