<?php

namespace App\Http\Controllers;

use App\Models\FinalProject;
use App\Models\FinalProjectSubmission;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FinalProjectController extends Controller
{
    public function create($courseSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        
        return view('final-projects.create', compact('course'));
    }

    public function store(Request $request, $courseSlug)
    {
        $course = Course::where('slug', $courseSlug)->firstOrFail();
        
        if ($course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date|after:now',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
            'is_active' => 'boolean',
        ]);

        $attachmentPaths = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('final-project-attachments', 'public');
                $attachmentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        $validated['course_id'] = $course->id;
        $validated['attachments'] = $attachmentPaths;
        $validated['is_active'] = $request->has('is_active');

        FinalProject::create($validated);

        return redirect()->route('courses.show', $courseSlug)
                        ->with('success', 'Final project created successfully!');
    }

    public function show($courseSlug, $finalProjectId)
    {
        $finalProject = FinalProject::with('course')->findOrFail($finalProjectId);
        $submission = null;
        
        if (auth()->check()) {
            $submission = $finalProject->getSubmissionByUser(auth()->id());
        }

        return view('final-projects.show', compact('finalProject', 'submission'));
    }

    public function submit(Request $request, $courseSlug, $finalProjectId)
    {
        $finalProject = FinalProject::findOrFail($finalProjectId);

        $existingSubmission = $finalProject->getSubmissionByUser(auth()->id());
        if ($existingSubmission) {
            return redirect()->back()->with('error', 'You have already submitted this project.');
        }

        $validated = $request->validate([
            'notes' => 'nullable|string',
            'attachments' => 'required|array',
            'attachments.*' => 'file|max:10240',
        ]);

        $attachmentPaths = [];
        foreach ($request->file('attachments') as $file) {
            $path = $file->store('final-project-submissions', 'public');
            $attachmentPaths[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
            ];
        }

        FinalProjectSubmission::create([
            'final_project_id' => $finalProjectId,
            'user_id' => auth()->id(),
            'notes' => $validated['notes'],
            'attachments' => $attachmentPaths,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Project submitted successfully!');
    }

    public function grade(Request $request, $courseSlug, $finalProjectId, $submissionId)
    {
        $submission = FinalProjectSubmission::with('finalProject.course')
                                           ->findOrFail($submissionId);

        if ($submission->finalProject->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'status' => 'graded',
            'graded_at' => now(),
            'graded_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Project graded successfully!');
    }

    public function submissions($courseSlug, $finalProjectId)
    {
        $finalProject = FinalProject::with(['course', 'submissions.user'])
                                   ->findOrFail($finalProjectId);

        if ($finalProject->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        return view('final-projects.submissions', compact('finalProject'));
    }

    public function edit($courseSlug, $finalProjectId)
    {
        $finalProject = FinalProject::with('course')->findOrFail($finalProjectId);
        
        if ($finalProject->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }
        
        return view('final-projects.edit', compact('finalProject'));
    }

    public function update(Request $request, $courseSlug, $finalProjectId)
    {
        $finalProject = FinalProject::with('course')->findOrFail($finalProjectId);
        
        if ($finalProject->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
            'is_active' => 'boolean',
        ]);

        $existingAttachments = $finalProject->attachments ?? [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('final-project-attachments', 'public');
                $existingAttachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                ];
            }
        }

        $validated['attachments'] = $existingAttachments;
        $validated['is_active'] = $request->has('is_active');

        $finalProject->update($validated);

        return redirect()->route('courses.show', $courseSlug)
                        ->with('success', 'Final project updated successfully!');
    }

    public function destroy($courseSlug, $finalProjectId)
    {
        $finalProject = FinalProject::with('course')->findOrFail($finalProjectId);
        
        if ($finalProject->course->instructor_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($finalProject->attachments) {
            foreach ($finalProject->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        foreach ($finalProject->submissions as $submission) {
            if ($submission->attachments) {
                foreach ($submission->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }
        }

        $finalProject->delete();

        return redirect()->route('courses.show', $courseSlug)
                        ->with('success', 'Final project deleted successfully!');
    }
}