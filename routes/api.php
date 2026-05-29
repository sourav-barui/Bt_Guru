<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Tenant API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['tenant', 'auth:sanctum'])->group(function () {
    // Student API
    Route::get('/student/profile', function (Request $request) {
        return $request->user()->load(['enrollments.course']);
    });

    Route::get('/student/courses', function (Request $request) {
        return $request->user()->enrollments()
            ->with('course')
            ->get();
    });

    Route::get('/student/notices', function (Request $request) {
        $tenant = app('current_tenant');
        return \App\Models\Notice::where('tenant_id', $tenant->id)
            ->active()
            ->forStudents()
            ->latest()
            ->get();
    });

    // Teacher API
    Route::get('/teacher/courses', function (Request $request) {
        return $request->user()->taughtCourses()
            ->with('enrollments.student')
            ->get();
    });

    // Tenant Admin API
    Route::get('/admin/stats', function (Request $request) {
        if (!$request->user()->isTenantAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $tenant = app('current_tenant');
        return [
            'students' => \App\Models\User::where('tenant_id', $tenant->id)
                ->whereHas('roles', fn($q) => $q->where('name', 'student'))
                ->count(),
            'teachers' => \App\Models\User::where('tenant_id', $tenant->id)
                ->whereHas('roles', fn($q) => $q->where('name', 'teacher'))
                ->count(),
            'courses' => \App\Models\Course::where('tenant_id', $tenant->id)->count(),
            'enrollments' => \App\Models\Enrollment::where('tenant_id', $tenant->id)->count(),
        ];
    });
});
