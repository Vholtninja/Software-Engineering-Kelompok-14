<x-app-layout>
    <x-slot name="title">Edit Course - {{ config('app.name') }}</x-slot>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="fw-bold mb-0">Edit Course: {{ $course->title }}</h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('courses.update', $course->slug) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold">Course Title</label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $course->title) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description', $course->description) }}</textarea>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="category" class="form-label fw-semibold">Category</label>
                                    <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $course->category) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="level" class="form-label fw-semibold">Level</label>
                                    <select class="form-select" id="level" name="level" required>
                                        <option value="beginner" {{ old('level', $course->level) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="intermediate" {{ old('level', $course->level) === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="advanced" {{ old('level', $course->level) === 'advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="thumbnail" class="form-label fw-semibold">Course Thumbnail</label>
                                @if($course->thumbnail)
                                    <div class="mb-2">
                                        <img src="{{ $course->thumbnail_url }}" alt="Current thumbnail" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                                <div class="form-text">Leave empty to keep the current thumbnail.</div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="price" class="form-label fw-semibold">Price (IDR)</label>
                                    <input type="number" class="form-control" id="price" name="price" value="{{ old('price', $course->price) }}" min="0" step="1000">
                                     <div class="form-text">Set to 0 for a free course.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="duration_minutes" class="form-label fw-semibold">Total Duration (minutes)</label>
                                    <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $course->duration_minutes) }}" min="0">
                                </div>
                            </div>
                            
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Course is Active (Visible to students)</label>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('courses.show', $course->slug) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle-fill me-2"></i>Update Course
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>