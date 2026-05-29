<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Book;
use App\Policies\TenantPolicy;
use App\Policies\CoursePolicy;
use App\Policies\EnrollmentPolicy;
use App\Policies\BookPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Tenant::class => TenantPolicy::class,
        Course::class => CoursePolicy::class,
        Enrollment::class => EnrollmentPolicy::class,
        Book::class => BookPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Super Admin gate - can do anything
        Gate::before(function (User $user) {
            if ($user->isSuperAdmin()) {
                return true;
            }
        });

        // Tenant Admin gate
        Gate::define('manage-tenant', function (User $user) {
            return $user->isTenantAdmin();
        });

        // Teacher gate
        Gate::define('teach', function (User $user) {
            return $user->isTeacher() || $user->isTenantAdmin();
        });

        // Student gate
        Gate::define('learn', function (User $user) {
            return $user->isStudent();
        });

        // Enrollment management
        Gate::define('manage-enrollments', function (User $user) {
            return $user->isTenantAdmin();
        });

        // Course access
        Gate::define('access-course', function (User $user, Course $course) {
            if ($user->isTenantAdmin()) {
                return $user->tenant_id === $course->tenant_id;
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
        });
    }
}
