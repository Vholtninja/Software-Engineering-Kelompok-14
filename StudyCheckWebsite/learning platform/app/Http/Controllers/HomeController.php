<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $newestCourses = Course::where('is_active', true)
                                ->with('instructor')
                                ->latest()
                                ->take(8)
                                ->get();
        
        $recentThreads = ForumThread::where('is_locked', false)
                                   ->with(['user', 'category'])
                                   ->latest()
                                   ->take(5)
                                   ->get();

        $stats = [
            'total_courses' => Course::where('is_active', true)->count(),
            'total_students' => User::where('role', 'student')->count(),
            'total_teachers' => User::whereIn('role', ['teacher', 'expert'])->count(),
        ];

        return view('home', compact('newestCourses', 'recentThreads', 'stats'));
    }

    public function dashboard()
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->role === 'teacher') {
            return $this->teacherDashboard();
        } elseif ($user->role === 'expert') {
            return $this->expertDashboard();
        } else {
            return $this->studentDashboard();
        }
    }

    private function studentDashboard()
    {
        $user = auth()->user();
        
        $enrolledCourseIds = \App\Models\UserProgress::where('user_id', $user->id)
                                                    ->whereNull('module_id')
                                                    ->pluck('course_id');

        $enrolledCourses = \App\Models\Course::whereIn('id', $enrolledCourseIds)
                                            ->with('instructor')
                                            ->where('is_active', true)
                                            ->get();

        $recentHomework = $user->homework()
                              ->with('course')
                              ->latest()
                              ->take(5)
                              ->get();
        return view('dashboard.student', compact('enrolledCourses', 'recentHomework'));
    }

    private function teacherDashboard()
    {
        $user = auth()->user();
        $myCourses = $user->courses()
                         ->withCount('students')
                         ->latest()
                         ->get();
        $pendingHomework = \App\Models\Homework::whereHas('course', function($query) use ($user) {
            $query->where('instructor_id', $user->id);
        })->where('status', 'pending')->count();
        return view('dashboard.teacher', compact('myCourses', 'pendingHomework'));
    }

    private function expertDashboard()
    {
        $user = auth()->user();
        $myCourses = $user->courses()
                         ->withCount('students')
                         ->latest()
                         ->get();
        $pendingHomework = \App\Models\Homework::whereHas('course', function($query) use ($user) {
            $query->where('instructor_id', $user->id);
        })->where('status', 'pending')->count();
        return view('dashboard.expert', compact('myCourses', 'pendingHomework'));
    }

    private function adminDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_courses' => Course::count(),
            'total_threads' => ForumThread::count(),
            'pending_verifications' => User::where('is_verified', false)->count(),
        ];
        
        $recentUsers = User::latest()->take(5)->get();

        return view('dashboard.admin', compact('stats', 'recentUsers'));
    }
}