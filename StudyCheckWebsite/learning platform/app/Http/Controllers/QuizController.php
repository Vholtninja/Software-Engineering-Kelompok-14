<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Module;
use App\Models\Question;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuizController extends Controller
{
    public function create($courseSlug, $moduleId)
    {
        $module = Module::with('course')->findOrFail($moduleId);
        if ($module->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        return view('quizzes.create', compact('module'));
    }

    public function store(Request $request, $courseSlug, $moduleId)
    {
        $module = Module::with('course')->findOrFail($moduleId);
        if ($module->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['module_id'] = $moduleId;
        $validated['is_active'] = $request->has('is_active');

        $quiz = Quiz::create($validated);
        return redirect()->route('quizzes.show', [$courseSlug, $moduleId, $quiz->id])
                        ->with('success', 'Quiz created successfully!');
    }

    public function show($courseSlug, $moduleId, $quizId)
    {
        $quiz = Quiz::with(['module.course', 'questions'])
                   ->findOrFail($quizId);
        $userAttempts = QuizAttempt::where('user_id', auth()->id())
                                  ->where('quiz_id', $quizId)
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        $lastAttempt = $userAttempts->first();
        $canRetake = true;
        $waitTime = null;
        if ($lastAttempt && $lastAttempt->passed) {
            $canRetake = false;
        } elseif ($userAttempts->count() >= $quiz->max_attempts) {
            $canRetake = false;
        } elseif ($lastAttempt && $lastAttempt->completed_at) {
            $waitTime = $lastAttempt->completed_at->addMinutes(5);
            if (now()->lt($waitTime)) {
                $canRetake = false;
            }
        }
        
        $nextModule = null;
        $currentModuleIndex = $quiz->module->course->modules->search(function($module) use ($quiz) {
            return $module->id === $quiz->module->id;
        });
        if ($quiz->module->course->modules->count() > $currentModuleIndex + 1) {
            $nextModule = $quiz->module->course->modules[$currentModuleIndex + 1];
        }

        return view('quizzes.show', compact('quiz', 'userAttempts', 'canRetake', 'lastAttempt', 'waitTime', 'nextModule'));
    }

    public function take($courseSlug, $moduleId, $quizId)
    {
        $quiz = Quiz::with(['module.course', 'questions'])
                   ->findOrFail($quizId);
        $userAttempts = QuizAttempt::where('user_id', auth()->id())
                                  ->where('quiz_id', $quizId)
                                  ->orderBy('created_at', 'desc')
                                  ->get();
        $lastAttempt = $userAttempts->first();
        if ($lastAttempt && $lastAttempt->passed) {
            return redirect()->route('quizzes.show', [$courseSlug, $moduleId, $quizId])
                           ->with('info', 'You have already passed this quiz.');
        }

        if ($userAttempts->count() >= $quiz->max_attempts) {
            return redirect()->route('quizzes.show', [$courseSlug, $moduleId, $quizId])
                           ->with('error', 'Maximum attempts reached for this quiz.');
        }

        if ($lastAttempt && $lastAttempt->completed_at) {
            $waitTime = $lastAttempt->completed_at->addMinutes(5);
            if (now()->lt($waitTime)) {
                $remainingTime = $waitTime->diffInMinutes(now());
                return redirect()->route('quizzes.show', [$courseSlug, $moduleId, $quizId])
                               ->with('error', "You must wait {$remainingTime} minutes before retaking the quiz.");
            }
        }

        if ($quiz->questions->isEmpty()) {
            return redirect()->route('quizzes.show', [$courseSlug, $moduleId, $quizId])
                           ->with('error', 'This quiz has no questions yet. Please contact the instructor.');
        }

        $questions = $quiz->questions()->inRandomOrder()->get();
        return view('quizzes.take', compact('quiz', 'questions'));
    }

    public function submit(Request $request, $courseSlug, $moduleId, $quizId)
    {
        $quiz = Quiz::with('questions', 'module.course')->findOrFail($quizId);
        $user = auth()->user();

        $validated = $request->validate([
            'answers' => 'required|array',
            'started_at' => 'required|date',
        ]);
        
        $startTime = Carbon::parse($validated['started_at']);
        $timeSpent = now()->diffInMinutes($startTime);

        if ($timeSpent > $quiz->time_limit) {
            return redirect()->route('quizzes.show', [$courseSlug, $moduleId, $quizId])
                           ->with('error', 'Time limit exceeded.');
        }

        $score = 0;
        $totalPointsEarned = 0;
        $totalQuestions = $quiz->questions->count();
        
        foreach ($quiz->questions as $question) {
            if (isset($validated['answers'][$question->id])) {
                $userAnswer = strtolower(trim($validated['answers'][$question->id]));
                $correctAnswer = strtolower(trim($question->correct_answer));
                if ($userAnswer === $correctAnswer) {
                    $score++;
                    $totalPointsEarned += $question->points;
                }
            }
        }
        
        if ($totalPointsEarned > 0) {
            $user->increment('points', $totalPointsEarned);
        }

        $percentage = ($totalQuestions > 0) ? ($score / $totalQuestions) * 100 : 0;
        $passed = $percentage >= $quiz->passing_score;

        QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quizId,
            'answers' => $validated['answers'],
            'score' => $score,
            'total_questions' => $totalQuestions,
            'percentage' => round($percentage, 2),
            'points_earned' => $totalPointsEarned,
            'passed' => $passed,
            'started_at' => $startTime,
            'completed_at' => now(),
        ]);
        
        if ($passed) {
            $moduleProgress = UserProgress::firstOrNew([
                'user_id' => $user->id,
                'course_id' => $quiz->module->course->id,
                'module_id' => $quiz->module->id,
            ]);
            $moduleProgress->progress_percentage = 100;
            $moduleProgress->is_completed = true;
            $moduleProgress->completed_at = now();
            $moduleProgress->save();

            $this->updateCourseProgress($user->id, $quiz->module->course->id);
            return redirect()->route('quizzes.show', [$courseSlug, $moduleId, $quizId])
                            ->with('success', 'Quiz completed successfully! You passed!');
        } else {
            return redirect()->route('quizzes.show', [$courseSlug, $moduleId, $quizId])
                            ->with('error', 'Quiz failed. You need to score at least ' . $quiz->passing_score . '% to pass.');
        }
    }

    private function updateCourseProgress($userId, $courseId)
    {
        $course = \App\Models\Course::with(['modules' => function($query) {
            $query->where('is_published', true);
        }])->find($courseId);
        $totalModules = $course->modules->count();
        
        if ($totalModules == 0) {
            $overallProgress = 0;
        } else {
            $completedModules = UserProgress::where('user_id', $userId)
                                           ->where('course_id', $courseId)
                                           ->whereNotNull('module_id')
                                           ->where('is_completed', true)
                                           ->count();
            $overallProgress = ($completedModules / $totalModules) * 100;
        }

        UserProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'course_id' => $courseId,
                'module_id' => null,
            ],
            [
                'progress_percentage' => round($overallProgress, 2),
                'is_completed' => $overallProgress >= 100,
                'completed_at' => $overallProgress >= 100 ? now() : null,
            ]
        );
    }

    public function edit($courseSlug, $moduleId, $quizId)
    {
        $quiz = Quiz::with('module.course')->findOrFail($quizId);
        if ($quiz->module->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        
        return view('quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, $courseSlug, $moduleId, $quizId)
    {
        $quiz = Quiz::with('module.course')->findOrFail($quizId);
        if ($quiz->module->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_limit' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:1|max:100',
            'max_attempts' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $quiz->update($validated);

        return redirect()->route('modules.show', [$courseSlug, $moduleId])
                        ->with('success', 'Quiz updated successfully!');
    }

    public function destroy($courseSlug, $moduleId, $quizId)
    {
        $quiz = Quiz::with('module.course')->findOrFail($quizId);
        if ($quiz->module->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $quiz->delete();

        return redirect()->route('modules.show', [$courseSlug, $moduleId])
                        ->with('success', 'Quiz deleted successfully!');
    }
}