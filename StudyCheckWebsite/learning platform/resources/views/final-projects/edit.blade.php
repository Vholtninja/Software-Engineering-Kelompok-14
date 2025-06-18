<x-app-layout>
    <x-slot name="title">Edit Final Project - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Edit Final Project: {{ $finalProject->title }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('final-projects.update', [$finalProject->course->slug, $finalProject->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Project Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $finalProject->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Project Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="6" required>{{ old('description', $finalProject->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="deadline" class="form-label">Submission Deadline</label>
                                <input type="datetime-local" class="form-control @error('deadline') is-invalid @enderror" 
                                       id="deadline" name="deadline" value="{{ old('deadline', $finalProject->deadline->format('Y-m-d\TH:i')) }}" required>
                                @error('deadline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($finalProject->attachments && count($finalProject->attachments) > 0)
                                <div class="mb-3">
                                    <label class="form-label">Current Files:</label>
                                    <div class="list-group mb-3">
                                        @foreach($finalProject->attachments as $index => $attachment)
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <i class="bi bi-file-earmark me-2"></i>
                                                    {{ $attachment['name'] }}
                                                    <small class="text-muted">({{ number_format($attachment['size'] / 1024, 2) }} KB)</small>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAttachment({{ $index }})">
                                                    Remove
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="attachments" class="form-label">Add New Files (Optional)</label>
                                <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                                       id="attachments" name="attachments[]" multiple>
                                @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Upload additional files. Max 10MB per file.</div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $finalProject->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Project is active and available for submission
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('final-projects.show', [$finalProject->course->slug, $finalProject->id]) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check me-2"></i>Update Project
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
        function removeAttachment(index) {
            if (confirm('Are you sure you want to remove this file?')) {
                fetch(`{{ route('final-projects.remove-attachment', [$finalProject->course->slug, $finalProject->id]) }}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ attachment_index: index })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
    @endpush
</x-app-layout>