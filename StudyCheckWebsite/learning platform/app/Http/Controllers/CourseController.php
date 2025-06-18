<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CourseController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $query = Course::where('is_active', true)->with('instructor');
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        $courses = $query->latest()->paginate(9);
        
        $categories = Course::distinct()->pluck('category');

        return view('courses.index', compact('courses', 'categories'));
    }

    public function show($slug)
    {
        $course = Course::where('slug', $slug)
                       ->with(['instructor', 'modules' => function($query) {
                           $query->where('is_published', true)->orderBy('order');
                       }])
                       ->firstOrFail();

        $studentCount = UserProgress::where('course_id', $course->id)
                                    ->whereNull('module_id')
                                    ->count();

        $isEnrolled = false;
        $progress = null;

        if (auth()->check()) {
            $progress = UserProgress::where('user_id', auth()->id())
                                   ->where('course_id', $course->id)
                                   ->whereNull('module_id')
                                   ->first();
            $isEnrolled = !!$progress;
        }

        return view('courses.show', compact('course', 'isEnrolled', 'progress', 'studentCount'));
    }

    public function enroll($id)
    {
        $course = Course::findOrFail($id);
        $user = auth()->user();

        $existingProgress = UserProgress::where('user_id', $user->id)
                                       ->where('course_id', $course->id)
                                       ->exists();
        if ($existingProgress) {
            return redirect()->back()->with('error', 'You are already enrolled in this course.');
        }

        UserProgress::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'module_id' => null,
            'progress_percentage' => 0,
            'is_completed' => false,
            'time_spent' => 0,
        ]);

        return redirect()->route('courses.learn', $course->slug)
                        ->with('success', 'Successfully enrolled in the course!');
    }

    public function learn($slug)
    {
        $course = Course::where('slug', $slug)
                       ->with([
                           'modules' => function($query) {
                               $query->where('is_published', true)->orderBy('order');
                           },
                           'modules.quizzes.quizAttempts' => function($query) {
                               $query->where('user_id', auth()->id());
                           },
                           'finalProjects'
                       ])
                       ->firstOrFail();

        if ($course->modules->isEmpty()) {
            return redirect()->route('courses.show', $slug)->with('error', 'This course does not have any published content yet.');
        }

        $progress = UserProgress::where('user_id', auth()->id())
                               ->where('course_id', $course->id)
                               ->whereNull('module_id')
                               ->first();

        if (!$progress) {
            return redirect()->route('courses.show', $slug)
                           ->with('error', 'You need to enroll in this course first.');
        }

        $currentModule = $course->modules->first();
        if (request()->has('module')) {
            $requestedModule = $course->modules->where('id', request('module'))->first();
            if ($requestedModule) {
                $currentModule = $requestedModule;
            }
        }
        
        $canAccessNextModule = true;
        $currentModuleIndex = $course->modules->search(function($module) use ($currentModule) {
            return $module->id === $currentModule->id;
        });

        if ($currentModuleIndex > 0) {
            $previousModule = $course->modules[$currentModuleIndex - 1];
            if ($previousModule->quizzes->isNotEmpty()) {
                $quiz = $previousModule->quizzes->first();
                $lastAttempt = $quiz->quizAttempts()->where('user_id', auth()->id())->where('passed', true)->first();
                if (!$lastAttempt) {
                    $canAccessNextModule = false;
                    return redirect()->route('quizzes.show', [$course->slug, $previousModule->id, $quiz->id])
                                   ->with('error', 'You must pass the quiz in the previous module to proceed.');
                }
            }

            $prevModuleProgress = UserProgress::where('user_id', auth()->id())
                                              ->where('module_id', $previousModule->id)
                                              ->where('is_completed', true)
                                              ->first();
            if (!$prevModuleProgress) {
                $canAccessNextModule = false;
                return redirect()->route('courses.learn', [$course->slug, 'module' => $previousModule->id])
                               ->with('error', 'Please complete the previous module content first.');
            }
        }

        return view('courses.learn', compact('course', 'currentModule', 'progress', 'canAccessNextModule'));
    }

    public function completeModule(Request $request, $courseId, $moduleId)
    {
        $user = auth()->user();
        $module = Module::with('quizzes')->findOrFail($moduleId);

        if ($module->quizzes->isNotEmpty()) {
            $quiz = $module->quizzes->first();
            $lastAttempt = $quiz->quizAttempts()->where('user_id', $user->id)->where('passed', true)->first();
            if (!$lastAttempt) {
                return response()->json(['success' => false, 'message' => 'You must pass the quiz to complete this module.'], 403);
            }
        }

        $moduleProgress = UserProgress::where('user_id', $user->id)
                                     ->where('course_id', $courseId)
                                     ->where('module_id', $moduleId)
                                     ->first();
        if (!$moduleProgress) {
            UserProgress::create([
                'user_id' => $user->id,
                'course_id' => $courseId,
                'module_id' => $moduleId,
                'progress_percentage' => 100,
                'is_completed' => true,
                'completed_at' => now(),
                'time_spent' => $request->input('time_spent', 0),
            ]);
        } elseif (!$moduleProgress->is_completed) {
            $moduleProgress->update([
                'progress_percentage' => 100,
                'is_completed' => true,
                'completed_at' => now(),
                'time_spent' => $moduleProgress->time_spent + $request->input('time_spent', 0),
            ]);
        }

        $this->updateCourseProgress($user->id, $courseId);

        return response()->json(['success' => true]);
    }

    private function updateCourseProgress($userId, $courseId)
    {
        $course = Course::with(['modules' => function($query) {
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

    public function create()
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Only teachers and experts can create courses.');
        }
        
        return view('courses.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Only teachers and experts can create courses.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $validated['instructor_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']);

        $course = Course::create($validated);
        return redirect()->route('courses.show', $course->slug)
                        ->with('success', 'Course created successfully!');
    }

    public function edit($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You can only edit your own courses.');
        }
        
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, $slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You can only edit your own courses.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        if ($validated['title'] !== $course->title) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $course->update($validated);

        $enrolledUsers = UserProgress::where('course_id', $course->id)
                                   ->whereNull('module_id')
                                   ->pluck('user_id');
        foreach ($enrolledUsers as $userId) {
            $this->updateCourseProgress($userId, $course->id);
        }

        return redirect()->route('courses.show', $course->slug)
                        ->with('success', 'Course updated successfully!');
    }

    public function destroy($slug)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You can only delete your own courses.');
        }

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        return redirect()->route('courses.index')
                        ->with('success', 'Course deleted successfully!');
    }
}