<x-app-layout>
    <x-slot name="title">{{ $finalProject->title }} - Final Project - {{ config('app.name') }}</x-slot>

    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0">{{ $finalProject->title }}</h4>
                        @if(auth()->check() && (auth()->id() === $finalProject->course->instructor_id || auth()->user()->isAdmin()))
                            <div class="btn-group">
                                 <a href="{{ route('final-projects.submissions', [$finalProject->course->slug, $finalProject->id]) }}" class="btn btn-sm btn-info">View Submissions</a>
                                <a href="{{ route('final-projects.edit', [$finalProject->course->slug, $finalProject->id]) }}" class="btn btn-sm btn-outline-secondary">Edit Project</a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <h6 class="fw-bold">Project Description:</h6>
                            <div class="p-3 bg-light rounded fs-5 lh-lg">
                                {!! nl2br(e($finalProject->description)) !!}
                            </div>
                        </div>

                        @if($finalProject->attachments && count($finalProject->attachments) > 0)
                         <div class="mb-4">
                                <h6 class="fw-bold">Project Resources:</h6>
                                <div class="list-group">
                                @foreach($finalProject->attachments as $attachment)
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

                        <div class="alert alert-info d-flex align-items-center">
                             <i class="bi bi-calendar-check-fill fs-4 me-3"></i>
                            <div>
                                <strong class="d-block">Deadline: {{ $finalProject->deadline->format('M d, Y, H:i') }}</strong>
                                @if($finalProject->deadline->isPast())
                                    <span class="badge bg-danger">Overdue</span>
                                @else
                                    <span class="badge bg-success">{{ $finalProject->deadline->diffForHumans() }} left</span>
                                @endif
                            </div>
                        </div>

                        @if(auth()->check() && auth()->id() !== $finalProject->course->instructor_id)
                             @if($submission)
                                <div class="card mt-4 border-success">
                                    <div class="card-header bg-success-subtle">
                                        <h5 class="fw-bold mb-0">Your Submission</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                             <span class="badge fs-6 bg-{{ $submission->status === 'graded' ? 'success' : 'warning' }}">{{ ucfirst($submission->status) }}</span>
                                             <small class="text-muted">Submitted {{ $submission->created_at->diffForHumans() }}</small>
                                        </div>

                                        @if($submission->notes)
                                            <h6 class="fw-bold">Your Notes:</h6>
                                            <p class="text-muted fst-italic">"{{ $submission->notes }}"</p>
                                        @endif

                                        <h6 class="fw-bold">Submitted Files:</h6>
                                        <div class="list-group">
                                        @foreach($submission->attachments as $attachment)
                                            <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="list-group-item list-group-item-action">
                                                <i class="bi bi-file-earmark-zip-fill me-2"></i> {{ $attachment['name'] }}
                                            </a>
                                        @endforeach
                                        </div>

                                        @if($submission->status === 'graded')
                                            <div class="alert alert-success mt-4">
                                                <h5 class="alert-heading fw-bold"><i class="bi bi-trophy-fill me-2"></i>Graded</h5>
                                                <p class="mb-1"><strong>Score:</strong> {{ $submission->score }}/100</p>
                                                @if($submission->feedback)
                                                    <p class="mb-0"><strong>Feedback:</strong> {{ $submission->feedback }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                @if($finalProject->deadline->isFuture())
                                    <div class="card mt-4">
                                        <div class="card-header bg-white py-3">
                                            <h5 class="fw-bold mb-0">Submit Your Project</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('final-projects.submit', [$finalProject->course->slug, $finalProject->id]) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-3">
                                                    <label for="attachments" class="form-label fw-semibold">Project Files (Required)</label>
                                                    <input type="file" class="form-control" name="attachments[]" multiple required>
                                                    <div class="form-text">You can upload multiple files. Max 10MB per file.</div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="notes" class="form-label fw-semibold">Notes (Optional)</label>
                                                    <textarea class="form-control" name="notes" rows="3" placeholder="Add any comments for your instructor..."></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-2"></i>Submit Project</button>
                                            </form>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-danger">The submission deadline has passed.</div>
                                @endif
                            @endif
                        @endif

                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">Project Info</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled d-grid gap-3">
                            <li><strong class="d-block">Course</strong> {{ $finalProject->course->title }}</li>
                            <li><strong class="d-block">Instructor</strong> {{ $finalProject->course->instructor->name }}</li>
                            <li><strong class="d-block">Created</strong> {{ $finalProject->created_at->format('M d, Y') }}</li>
                        </ul>
                         <a href="{{ route('courses.show', $finalProject->course->slug) }}" class="btn btn-outline-primary btn-sm w-100 mt-3">
                            <i class="bi bi-arrow-left me-1"></i>Back to Course
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>