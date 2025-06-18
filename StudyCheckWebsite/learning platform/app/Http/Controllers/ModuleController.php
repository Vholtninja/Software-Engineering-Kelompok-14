<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use App\Models\UserProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ModuleController extends Controller
{
    use AuthorizesRequests;

    public function create($courseSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You can only add modules to your own courses.');
        }
        
        return view('modules.create', compact('course'));
    }

    public function store(Request $request, $courseSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You can only add modules to your own courses.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'video_url' => 'nullable|url',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('module-attachments', 'public');
                $attachmentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        $validated['course_id'] = $course->id;
        $validated['attachments'] = $attachmentPaths;
        
        if (!isset($validated['order'])) {
            $validated['order'] = Module::where('course_id', $course->id)->max('order') + 1;
        }

        $validated['is_published'] = $request->has('is_published') ? true : false;

        Module::create($validated);

        $enrolledUsers = UserProgress::where('course_id', $course->id)
                                   ->whereNull('module_id')
                                   ->pluck('user_id');

        foreach ($enrolledUsers as $userId) {
            $this->updateCourseProgress($userId, $course->id);
        }

        return redirect()->route('courses.show', $course->slug)
                        ->with('success', 'Module created successfully!');
    }

    public function show($courseSlug, $moduleId)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $module = Module::where('course_id', $course->id)
                        ->where('id', $moduleId)
                        ->with(['quizzes.questions'])
                        ->firstOrFail();

        if (!$module->is_published && $course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(404);
        }

        return view('modules.show', compact('course', 'module'));
    }

    public function edit($courseSlug, $moduleId)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $module = Module::where('course_id', $course->id)
                        ->where('id', $moduleId)
                        ->firstOrFail();
        
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You can only edit modules in your own courses.');
        }
        
        return view('modules.edit', compact('course', 'module'));
    }

    public function update(Request $request, $courseSlug, $moduleId)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $module = Module::where('course_id', $course->id)
                        ->where('id', $moduleId)
                        ->firstOrFail();
        
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You can only edit modules in your own courses.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'video_url' => 'nullable|url',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        $existingAttachments = $module->attachments ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('module-attachments', 'public');
                $existingAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        $validated['attachments'] = $existingAttachments;
        $validated['is_published'] = $request->has('is_published') ? true : false;
        
        $module->update($validated);

        $enrolledUsers = UserProgress::where('course_id', $course->id)
                                   ->whereNull('module_id')
                                   ->pluck('user_id');

        foreach ($enrolledUsers as $userId) {
            $this->updateCourseProgress($userId, $course->id);
        }

        return redirect()->route('modules.show', [$course->slug, $module->id])
                        ->with('success', 'Module updated successfully!');
    }

    public function destroy($courseSlug, $moduleId)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $module = Module::where('course_id', $course->id)
                        ->where('id', $moduleId)
                        ->firstOrFail();
        
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You can only delete modules in your own courses.');
        }

        if ($module->attachments) {
            foreach ($module->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        UserProgress::where('module_id', $moduleId)->delete();

        $module->delete();

        $enrolledUsers = UserProgress::where('course_id', $course->id)
                                   ->whereNull('module_id')
                                   ->pluck('user_id');

        foreach ($enrolledUsers as $userId) {
            $this->updateCourseProgress($userId, $course->id);
        }

        return redirect()->route('courses.show', $course->slug)
                        ->with('success', 'Module deleted successfully!');
    }

    public function removeAttachment(Request $request, $courseSlug, $moduleId)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        $module = Module::where('course_id', $course->id)
                        ->where('id', $moduleId)
                        ->firstOrFail();
        
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'You can only modify modules in your own courses.');
        }

        $attachmentIndex = $request->input('attachment_index');
        $attachments = $module->attachments ?? [];
        
        if (isset($attachments[$attachmentIndex])) {
            Storage::disk('public')->delete($attachments[$attachmentIndex]['path']);
            unset($attachments[$attachmentIndex]);
            $module->update(['attachments' => array_values($attachments)]);
        }

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
}