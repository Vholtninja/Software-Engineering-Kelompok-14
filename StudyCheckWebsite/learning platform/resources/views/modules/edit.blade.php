<x-app-layout>
    <x-slot name="title">Edit Module - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Edit Module: {{ $module->title }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('modules.update', [$module->course->slug, $module->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Module Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title', $module->title) }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description (Optional)</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="2">{{ old('description', $module->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Module Content</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" name="content" rows="10" required>{{ old('content', $module->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">You can use basic HTML tags for formatting.</div>
                            </div>

                            <div class="mb-3">
                                <label for="video_url" class="form-label">Video URL (Optional)</label>
                                <input type="url" class="form-control @error('video_url') is-invalid @enderror" 
                                       id="video_url" name="video_url" value="{{ old('video_url', $module->video_url) }}">
                                @error('video_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">YouTube, Vimeo, or direct video file URL</div>
                            </div>

                            <div class="mb-3">
                                <label for="attachments" class="form-label">Attachments (Optional)</label>
                                @if($module->attachments && count($module->attachments) > 0)
                                    <div class="mb-2">
                                        <h6 class="small mb-1">Current Files:</h6>
                                        <ul class="list-group list-group-flush">
                                            @foreach($module->attachments as $index => $attachment)
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                                    <div>
                                                        <i class="bi bi-file-earmark me-2"></i>
                                                        {{ $attachment['name'] }} 
                                                        <small class="text-muted">({{ number_format($attachment['size'] / 1024, 2) }} KB)</small>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAttachment({{ $index }})">Remove</button>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                                       id="attachments" name="attachments[]" multiple>
                                @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">You can select multiple files. Max 10MB per file.</div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="order" class="form-label">Order</label>
                                    <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                           id="order" name="order" value="{{ old('order', $module->order) }}" min="0">
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Leave empty to add at the end</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control @error('duration_minutes') is-invalid @enderror" 
                                           id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $module->duration_minutes) }}" min="0">
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_published" name="is_published" value="1" 
                                           {{ old('is_published', $module->is_published) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_published">
                                        Publish module (make it visible to students)
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('modules.show', [$module->course->slug, $module->id]) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check me-2"></i>Update Module
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
                fetch(`{{ route('modules.remove-attachment', [$module->course->slug, $module->id]) }}`, {
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