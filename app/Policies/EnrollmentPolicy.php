<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Enrollment;

class EnrollmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isTenantAdmin() || $user->isTeacher();
    }

    public function view(User $user, Enrollment $enrollment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->tenant_id !== $enrollment->tenant_id) {
            return false;
        }

        if ($user->isTenantAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $enrollment->course->teachers()->where('teacher_id', $user->id)->exists();
        }

        if ($user->isStudent()) {
            return $enrollment->student_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isTenantAdmin() || $user->isStudent();
    }

    public function update(User $user, Enrollment $enrollment): bool
    {
        return $user->isTenantAdmin() && $user->tenant_id === $enrollment->tenant_id;
    }

    public function delete(User $user, Enrollment $enrollment): bool
    {
        return $user->isTenantAdmin() && $user->tenant_id === $enrollment->tenant_id;
    }

    public function approve(User $user, Enrollment $enrollment): bool
    {
        return $user->isTenantAdmin() && $user->tenant_id === $enrollment->tenant_id;
    }

    public function managePayment(User $user, Enrollment $enrollment): bool
    {
        return $user->isTenantAdmin() && $user->tenant_id === $enrollment->tenant_id;
    }
}
