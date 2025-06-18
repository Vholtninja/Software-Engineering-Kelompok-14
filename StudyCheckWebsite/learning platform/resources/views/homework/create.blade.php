<!-- resources/views/homework/create.blade.php -->
<x-app-layout>
    <x-slot name="title">Ask Homework Question - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Ask Homework Question</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('homework.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Question Title</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Be specific and clear about what you need help with</div>
                            </div>

                            <div class="mb-3">
                                <label for="course_id" class="form-label">Course</label>
                                <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                                    <option value="">Select a course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="subject" class="form-label">Subject</label>
                                    <select class="form-select @error('subject') is-invalid @enderror" id="subject" name="subject" required>
                                        <option value="">Select subject</option>
                                        <option value="math" {{ old('subject') === 'math' ? 'selected' : '' }}>Math</option>
                                        <option value="science" {{ old('subject') === 'science' ? 'selected' : '' }}>Science</option>
                                        <option value="english" {{ old('subject') === 'english' ? 'selected' : '' }}>English</option>
                                        <option value="history" {{ old('subject') === 'history' ? 'selected' : '' }}>History</option>
                                        <option value="other" {{ old('subject') === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="difficulty" class="form-label">Difficulty</label>
                                    <select class="form-select @error('difficulty') is-invalid @enderror" id="difficulty" name="difficulty" required>
                                        <option value="">Select difficulty</option>
                                        <option value="easy" {{ old('difficulty') === 'easy' ? 'selected' : '' }}>Easy</option>
                                        <option value="medium" {{ old('difficulty') === 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="hard" {{ old('difficulty') === 'hard' ? 'selected' : '' }}>Hard</option>
                                    </select>
                                    @error('difficulty')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Provide context about your assignment or homework</div>
                            </div>

                            <div class="mb-3">
                                <label for="question" class="form-label">Your Question</label>
                                <textarea class="form-control @error('question') is-invalid @enderror" 
                                          id="question" name="question" rows="6" required>{{ old('question') }}</textarea>
                                @error('question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Be specific about what you're struggling with and what kind of help you need</div>
                            </div>

                            <div class="mb-3">
                                <label for="attachments" class="form-label">Attachments (Optional)</label>
                                <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" 
                                       id="attachments" name="attachments[]" multiple>
                                @error('attachments.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">You can attach images, documents, or other files related to your question. Max 5MB per file.</div>
                            </div>

                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date (Optional)</label>
                                <input type="datetime-local" class="form-control @error('due_date') is-invalid @enderror" 
                                       id="due_date" name="due_date" value="{{ old('due_date') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">When do you need the answer by?</div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('homework.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>Ask Question
                                    <span class="loading">
                                        <i class="bi bi-hourglass-split me-2"></i>Submitting...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tips -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-lightbulb me-2"></i>Tips for Getting Better Help
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Be specific about what you're struggling with</li>
                            <li>Show what you've already tried</li>
                            <li>Include relevant context about your assignment</li>
                            <li>Attach images or documents if they help explain your question</li>
                            <li>Use proper grammar and formatting to make your question clear</li>
                            <li>Set a realistic due date if you have one</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>