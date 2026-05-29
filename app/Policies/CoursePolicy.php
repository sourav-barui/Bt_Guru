<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isTenantAdmin() || $user->isTeacher() || $user->isStudent();
    }

    public function view(User $user, Course $course): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->tenant_id !== $course->tenant_id) {
            return false;
        }

        if ($user->isTenantAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $course->teachers()->where('teacher_id', $user->id)->exists();
        }

        if ($user->isStudent()) {
            return $course->enrollments()
                ->where('student_id', $user->id)
                ->where('enrollment_status', 'active')
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isTenantAdmin();
    }

    public function update(User $user, Course $course): bool
    {
        return $user->isTenantAdmin() && $user->tenant_id === $course->tenant_id;
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->isTenantAdmin() && $user->tenant_id === $course->tenant_id;
    }

    public function manageTeachers(User $user, Course $course): bool
    {
        return $user->isTenantAdmin() && $user->tenant_id === $course->tenant_id;
    }

    public function enrollStudents(User $user, Course $course): bool
    {
        return $user->isTenantAdmin() && $user->tenant_id === $course->tenant_id;
    }
}
