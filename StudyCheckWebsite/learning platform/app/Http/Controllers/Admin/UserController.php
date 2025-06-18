<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        if ($request->has('verified') && $request->verified !== '') {
            $query->where('is_verified', $request->verified);
        }

        $users = $query->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:student,teacher,expert,moderator,admin',
            'bio' => 'nullable|string|max:1000',
            'institution' => 'nullable|string|max:255',
            'level' => 'required|in:beginner,intermediate,advanced',
            'is_verified' => 'boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_verified'] = $request->has('is_verified');
        $validated['email_verified_at'] = $validated['is_verified'] ? now() : null;

        User::create($validated);

        return redirect()->route('admin.users.index')
                        ->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        $user->load(['courses', 'enrollments', 'forumThreads', 'forumReplies']);
        
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:student,teacher,expert,moderator,admin',
            'bio' => 'nullable|string|max:1000',
            'institution' => 'nullable|string|max:255',
            'level' => 'required|in:beginner,intermediate,advanced',
            'is_verified' => 'boolean',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $validated['is_verified'] = $request->has('is_verified');
        
        if ($validated['is_verified'] && !$user->email_verified_at) {
            $validated['email_verified_at'] = now();
        } elseif (!$validated['is_verified']) {
            $validated['email_verified_at'] = null;
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
                        ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'User deleted successfully!');
    }

    public function verify(User $user)
    {
        $user->update([
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        return redirect()->back()->with('success', 'User verified successfully!');
    }

    public function unverify(User $user)
    {
        $user->update([
            'is_verified' => false,
            'email_verified_at' => null,
        ]);

        return redirect()->back()->with('success', 'User unverified successfully!');
    }

    public function resetPassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password reset successfully!');
    }
}