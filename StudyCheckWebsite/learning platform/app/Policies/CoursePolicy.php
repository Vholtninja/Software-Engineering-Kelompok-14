<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Course $course): bool
    {
        return $course->is_active || $user->id === $course->instructor_id || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    public function update(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id || $user->isAdmin();
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->id === $course->instructor_id || $user->isAdmin();
    }
}