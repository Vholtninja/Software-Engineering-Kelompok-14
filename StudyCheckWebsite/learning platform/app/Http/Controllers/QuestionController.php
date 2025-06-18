<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function create($courseSlug, $moduleId, $quizId)
    {
        $quiz = Quiz::findOrFail($quizId);
        return view('questions.create', compact('quiz'));
    }

    public function store(Request $request, $courseSlug, $moduleId, $quizId)
    {
        $validated = $request->validate([
            'question' => 'required|string|min:5',
            'type' => 'required|in:multiple_choice,true_false',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*' => 'required_if:type,multiple_choice|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:0',
        ]);
        
        $validated['quiz_id'] = $quizId;
        
        Question::create($validated);
        
        return redirect()->route('quizzes.show', [$courseSlug, $moduleId, $quizId])
                        ->with('success', 'Question added successfully!');
    }

    public function edit($courseSlug, $moduleId, $quizId, $questionId)
    {
        $question = Question::findOrFail($questionId);
        return view('questions.edit', compact('question'));
    }

    public function update(Request $request, $courseSlug, $moduleId, $quizId, $questionId)
    {
        $question = Question::findOrFail($questionId);

        $validated = $request->validate([
            'question' => 'required|string|min:5',
            'type' => 'required|in:multiple_choice,true_false',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*' => 'required_if:type,multiple_choice|string',
            'correct_answer' => 'required|string',
            'points' => 'required|integer|min:0',
        ]);
        
        $question->update($validated);
        
        return redirect()->route('quizzes.show', [$courseSlug, $moduleId, $quizId])
                        ->with('success', 'Question updated successfully!');
    }

    public function destroy($courseSlug, $moduleId, $quizId, $questionId)
    {
        $question = Question::findOrFail($questionId);
        $question->delete();
        
        return redirect()->route('quizzes.show', [$courseSlug, $moduleId, $quizId])
                        ->with('success', 'Question deleted successfully!');
    }
}