<x-app-layout>
    <x-slot name="title">{{ $finalProject->title }} - Submissions - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Submissions for: {{ $finalProject->title }}</h4>
                        <a href="{{ route('final-projects.show', [$finalProject->course->slug, $finalProject->id]) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Project
                        </a>
                    </div>
                    <div class="card-body">
                        @if($finalProject->submissions->count() > 0)
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $finalProject->submissions->count() }}</h3>
                                            <small>Total Submissions</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $finalProject->submissions->where('status', 'graded')->count() }}</h3>
                                            <small>Graded</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $finalProject->submissions->where('status', 'pending')->count() }}</h3>
                                            <small>Pending</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h3>{{ $finalProject->submissions->where('status', 'graded')->avg('score') ? round($finalProject->submissions->where('status', 'graded')->avg('score'), 1) : 0 }}</h3>
                                            <small>Average Score</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Submitted</th>
                                            <th>Files</th>
                                            <th>Status</th>
                                            <th>Score</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($finalProject->submissions as $submission)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $submission->user->avatar_url }}" alt="{{ $submission->user->name }}" class="avatar avatar-sm me-2">
                                                        <div>
                                                            <h6 class="mb-0">{{ $submission->user->name }}</h6>
                                                            <small class="text-muted">{{ $submission->user->email }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $submission->created_at->format('M d, Y H:i') }}
                                                    @if($submission->created_at->gt($finalProject->deadline))
                                                        <span class="badge bg-danger ms-1">Late</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ count($submission->attachments) }} files</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $submission->status === 'graded' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($submission->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($submission->status === 'graded')
                                                        <span class="fw-bold">{{ $submission->score }}/100</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#submissionModal{{ $submission->id }}">
                                                        View & Grade
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                                <h4 class="text-muted mt-3">No submissions yet</h4>
                                <p class="text-muted">Students haven't submitted their projects yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($finalProject->submissions as $submission)
        <div class="modal fade" id="submissionModal{{ $submission->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $submission->user->name }}'s Submission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <strong>Submitted:</strong> {{ $submission->created_at->format('M d, Y \a\t H:i') }}
                            @if($submission->created_at->gt($finalProject->deadline))
                                <span class="badge bg-danger ms-2">Late Submission</span>
                            @endif
                        </div>

                        @if($submission->notes)
                            <div class="mb-3">
                                <strong>Student Notes:</strong>
                                <div class="p-2 bg-light rounded">{{ $submission->notes }}</div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <strong>Submitted Files:</strong>
                            <div class="list-group mt-2">
                                @foreach($submission->attachments as $attachment)
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

                        @if($submission->status === 'graded')
                            <div class="alert alert-success">
                                <h6><i class="bi bi-check-circle me-2"></i>Already Graded</h6>
                                <p class="mb-1"><strong>Score:</strong> {{ $submission->score }}/100</p>
                                @if($submission->feedback)
                                    <p class="mb-1"><strong>Feedback:</strong> {{ $submission->feedback }}</p>
                                @endif
                                <small class="text-muted">Graded on {{ $submission->graded_at->format('M d, Y') }}</small>
                            </div>
                        @endif

                        <form action="{{ route('final-projects.grade', [$finalProject->course->slug, $finalProject->id, $submission->id]) }}" method="POST">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="score{{ $submission->id }}" class="form-label">Score (0-100)</label>
                                    <input type="number" class="form-control" id="score{{ $submission->id }}" name="score" 
                                           value="{{ $submission->score }}" min="0" max="100" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="feedback{{ $submission->id }}" class="form-label">Feedback</label>
                                <textarea class="form-control" id="feedback{{ $submission->id }}" name="feedback" rows="3">{{ $submission->feedback }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>{{ $submission->status === 'graded' ? 'Update Grade' : 'Grade Submission' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</x-app-layout>