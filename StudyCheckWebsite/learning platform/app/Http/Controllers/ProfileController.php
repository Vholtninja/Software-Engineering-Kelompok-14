<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show($id = null)
    {
        $user = $id ? \App\Models\User::findOrFail($id) : auth()->user();
        
        $stats = [
            'courses_created' => $user->courses()->count(),
            'courses_enrolled' => $user->enrollments()->wherePivotNull('module_id')->count(),
            'forum_threads' => $user->forumThreads()->count(),
            'forum_replies' => $user->forumReplies()->count(),
        ];
        
        $recentCourses = collect();

        if ($user->role === 'student') {
            $recentCourses = $user->enrollments()
                                  ->wherePivotNull('module_id')
                                  ->latest('pivot_created_at')
                                  ->take(5)
                                  ->get();
        } else {
            $recentCourses = $user->courses()->latest()->take(5)->get();
        }

        $recentThreads = $user->forumThreads()->with('category')->latest()->take(3)->get();
        return view('profile.show', compact('user', 'stats', 'recentCourses', 'recentThreads'));
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}