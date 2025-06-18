<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\HomeworkAnswer;
use App\Models\Course;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HomeworkController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Homework::with(['student', 'course']);
        $user = auth()->user();

        if ($user->role === 'student') {
            $query->where('student_id', $user->id);
        } elseif ($user->role === 'teacher') {
            $query->whereHas('course', function($q) use ($user) {
                $q->where('instructor_id', $user->id);
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('subject') && $request->subject) {
            $query->where('subject', $request->subject);
        }

        if ($request->has('difficulty') && $request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }

        $homework = $query->latest()->paginate(15);

        return view('homework.index', compact('homework'));
    }

    public function show($id)
    {
        $homework = Homework::with(['student', 'course', 'answers.teacher'])
                           ->findOrFail($id);
        if (!$this->canViewHomework($homework)) {
            abort(403);
        }

        $answers = $homework->answers()
                           ->with('teacher')
                           ->orderBy('is_best_answer', 'desc')
                           ->orderBy('upvotes', 'desc')
                           ->orderBy('created_at', 'asc')
                           ->get();
        return view('homework.show', compact('homework', 'answers'));
    }

    public function create()
    {
        $courses = collect();
        if (auth()->user()->role === 'student') {
            $enrolledCourseIds = UserProgress::where('user_id', auth()->id())
                                             ->whereNull('module_id')
                                             ->pluck('course_id');
            
            $courses = Course::whereIn('id', $enrolledCourseIds)
                             ->where('is_active', true)
                             ->get();
        } else {
            $courses = Course::where('is_active', true)->get();
        }

        return view('homework.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'question' => 'required|string|min:10',
            'course_id' => 'required|exists:courses,id',
            'subject' => 'required|in:math,science,english,history,other',
            'difficulty' => 'required|in:easy,medium,hard',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120',
            'due_date' => 'nullable|date|after:now',
        ]);
        
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('homework-attachments', 'public');
                $attachmentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType(),
                ];
            }
        }

        $validated['student_id'] = auth()->id();
        $validated['attachments'] = $attachmentPaths;

        $homework = Homework::create($validated);

        return redirect()->route('homework.show', $homework->id)
                        ->with('success', 'Homework question submitted successfully!');
    }

    public function answer(Request $request, $id)
    {
        $homework = Homework::findOrFail($id);
        $allowedRoles = ['teacher', 'expert', 'admin'];

        if (!in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'Only teachers, experts, and admins can answer homework questions.');
        }

        $validated = $request->validate([
            'answer' => 'required|string|min:10',
            'explanation' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120',
        ]);
        
        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('homework-answer-attachments', 'public');
                $attachmentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType(),
                ];
            }
        }

        $validated['homework_id'] = $homework->id;
        $validated['teacher_id'] = auth()->id();
        $validated['attachments'] = $attachmentPaths;

        HomeworkAnswer::create($validated);

        if ($homework->status === 'pending') {
            $homework->update(['status' => 'answered']);
        }

        return redirect()->back()->with('success', 'Answer submitted successfully!');
    }

    public function markBestAnswer(Request $request, $answerId)
    {
        $answer = HomeworkAnswer::findOrFail($answerId);
        $homework = $answer->homework;

        if ($homework->student_id !== auth()->id()) {
            abort(403);
        }

        HomeworkAnswer::where('homework_id', $homework->id)
                      ->update(['is_best_answer' => false]);
        $answer->update(['is_best_answer' => true]);
        
        $homework->update(['status' => 'closed']);

        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $homework = Homework::findOrFail($id);
        if ($homework->student_id !== auth()->id()) {
            abort(403);
        }

        $enrolledCourseIds = UserProgress::where('user_id', auth()->id())
                                         ->whereNull('module_id')
                                         ->pluck('course_id');
        
        $courses = Course::whereIn('id', $enrolledCourseIds)
                         ->where('is_active', true)
                         ->get();

        return view('homework.edit', compact('homework', 'courses'));
    }

    public function update(Request $request, $id)
    {
        $homework = Homework::findOrFail($id);
        if ($homework->student_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'question' => 'required|string|min:10',
            'course_id' => 'required|exists:courses,id',
            'subject' => 'required|in:math,science,english,history,other',
            'difficulty' => 'required|in:easy,medium,hard',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120',
            'due_date' => 'nullable|date|after:now',
        ]);
        
        $existingAttachments = $homework->attachments ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('homework-attachments', 'public');
                $existingAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getClientMimeType(),
                ];
            }
        }

        $validated['attachments'] = $existingAttachments;
        $homework->update($validated);

        return redirect()->route('homework.show', $homework->id)
                        ->with('success', 'Homework updated successfully!');
    }

    public function destroy($id)
    {
        $homework = Homework::findOrFail($id);
        if ($homework->student_id !== auth()->id() && !auth()->user()->isModerator()) {
            abort(403);
        }

        if ($homework->attachments) {
            foreach ($homework->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        foreach ($homework->answers as $answer) {
            if ($answer->attachments) {
                foreach ($answer->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }
        }

        $homework->delete();
        return redirect()->route('homework.index')
                        ->with('success', 'Homework deleted successfully!');
    }

    private function canViewHomework($homework)
    {
        $user = auth()->user();
        if ($homework->student_id === $user->id) {
            return true;
        }

        if ($user->isTeacher() && $homework->course->instructor_id === $user->id) {
            return true;
        }

        if (in_array($user->role, ['expert', 'moderator', 'admin'])) {
            return true;
        }

        return false;
    }
}