<x-app-layout>
    <x-slot name="title">Add Question - {{ $quiz->title }}</x-slot>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="fw-bold mb-0">Add New Question</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('questions.store', [$quiz->module->course->slug, $quiz->module->id, $quiz->id]) }}" method="POST" id="question-form">
                            @csrf
                            <div class="mb-3">
                                <label for="question" class="form-label">Question Text</label>
                                <textarea name="question" id="question" class="form-control" rows="3" required>{{ old('question') }}</textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="type" class="form-label">Question Type</label>
                                    <select name="type" id="type" class="form-select">
                                        <option value="multiple_choice" selected>Multiple Choice</option>
                                        <option value="true_false">True/False</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="points" class="form-label">Points</label>
                                    <input type="number" name="points" id="points" class="form-control" value="{{ old('points', 10) }}" min="0" required>
                                </div>
                            </div>
                            
                            <div id="options-container">
                                </div>
                            
                            <div class="mb-3">
                                <label for="correct_answer" class="form-label">Correct Answer</label>
                                <input type="text" name="correct_answer" id="correct_answer" class="form-control" value="{{ old('correct_answer') }}" required>
                                <small class="form-text text-muted" id="correct_answer_help">For multiple choice, enter the exact text of the correct option. For true/false, enter "True" or "False".</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('quizzes.show', [$quiz->module->course->slug, $quiz->module->id, $quiz->id]) }}" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Question</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('type');
            const optionsContainer = document.getElementById('options-container');
            const correctAnswerHelp = document.getElementById('correct_answer_help');

            function renderOptions() {
                if (typeSelect.value === 'multiple_choice') {
                    optionsContainer.innerHTML = `
                        <div class="mb-3">
                            <label class="form-label">Options</label>
                            <div id="mc-options">
                                <div class="input-group mb-2">
                                    <input type="text" name="options[]" class="form-control" required>
                                    <button class="btn btn-outline-danger remove-option-btn" type="button">Remove</button>
                                </div>
                                <div class="input-group mb-2">
                                    <input type="text" name="options[]" class="form-control" required>
                                    <button class="btn btn-outline-danger remove-option-btn" type="button">Remove</button>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" type="button" id="add-option-btn">Add Option</button>
                        </div>
                    `;
                    correctAnswerHelp.textContent = 'For multiple choice, enter the exact text of the correct option.';
                } else {
                    optionsContainer.innerHTML = '';
                    correctAnswerHelp.textContent = 'For true/false, enter "True" or "False".';
                }
            }
            
            optionsContainer.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-option-btn')) {
                    e.target.closest('.input-group').remove();
                }
                if (e.target.id === 'add-option-btn') {
                    const div = document.createElement('div');
                    div.className = 'input-group mb-2';
                    div.innerHTML = `
                        <input type="text" name="options[]" class="form-control" required>
                        <button class="btn btn-outline-danger remove-option-btn" type="button">Remove</button>
                    `;
                    document.getElementById('mc-options').appendChild(div);
                }
            });

            typeSelect.addEventListener('change', renderOptions);
            renderOptions();
        });
    </script>
    @endpush
</x-app-layout>