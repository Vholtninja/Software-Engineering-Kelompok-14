<!-- resources/views/forum/create-thread.blade.php -->
<x-app-layout>
    <x-slot name="title">Start New Discussion - Forum - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Start New Discussion</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('forum.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id', $selectedCategory?->id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Discussion Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Be specific and descriptive</div>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" 
                                          id="content" name="content" rows="8" required>{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Provide details, context, and any relevant information</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('forum.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>Start Discussion
                                    <span class="loading">
                                        <i class="bi bi-hourglass-split me-2"></i>Posting...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Forum Guidelines -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-info-circle me-2"></i>Discussion Guidelines
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Be respectful and constructive in your discussions</li>
                            <li>Search for existing discussions before creating a new one</li>
                            <li>Use clear and descriptive titles</li>
                            <li>Stay on topic and provide relevant details</li>
                            <li>Help others by sharing your knowledge and experience</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>