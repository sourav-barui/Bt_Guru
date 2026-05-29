<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;
        $courses = Course::where('tenant_id', $tenant->id)
            ->latest()
            ->withCount('enrollments')
            ->paginate(15);

        return view('tenant.courses.index', compact('courses'));
    }

    public function create()
    {
        $teachers = User::where('tenant_id', Auth::user()->tenant_id)
            ->whereHas('roles', function ($q) { $q->where('name', 'teacher'); })
            ->get();

        return view('tenant.courses.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $tenant = Auth::user()->tenant;

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fees' => 'required|numeric|min:0',
            'fees_type' => 'required|in:one_time,monthly',
            'past_month_fee' => 'nullable|numeric|min:0',
            'duration' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:users,id',
            'status' => 'required|in:active,inactive,draft',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null;
        $endDate   = $request->end_date   ? \Carbon\Carbon::parse($request->end_date)   : null;
        $duration  = ($startDate && $endDate)
            ? $this->calcDuration($startDate, $endDate)
            : $request->duration;

        $data = [
            'tenant_id' => $tenant->id,
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'fees' => $request->fees,
            'fees_type' => $request->fees_type,
            'past_month_fee' => $request->fees_type === 'monthly' ? ($request->past_month_fee ?? 0) : 0,
            'duration'   => $duration,
            'start_date' => $startDate?->toDateString(),
            'end_date'   => $endDate?->toDateString(),
            'status' => $request->status,
        ];

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('courses', 'public');
            $data['thumbnail'] = $path;
        }

        $course = Course::create($data);

        // Attach teachers
        if ($request->teachers) {
            $teachers = [];
            foreach ($request->teachers as $index => $teacherId) {
                $teachers[$teacherId] = ['is_primary' => $index === 0];
            }
            $course->teachers()->attach($teachers);
        }

        return redirect()->route('tenant.courses.index')
            ->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $this->authorize('view', $course);
        $course->load(['teachers', 'enrollments.student']);
        return view('tenant.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $this->authorize('update', $course);
        
        $teachers = User::where('tenant_id', Auth::user()->tenant_id)
            ->whereHas('roles', function ($q) { $q->where('name', 'teacher'); })
            ->get();

        $courseTeacherIds = $course->teachers->pluck('id')->toArray();

        return view('tenant.courses.edit', compact('course', 'teachers', 'courseTeacherIds'));
    }

    public function update(Request $request, Course $course)
    {
        $this->authorize('update', $course);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fees' => 'required|numeric|min:0',
            'fees_type' => 'required|in:one_time,monthly',
            'past_month_fee' => 'nullable|numeric|min:0',
            'duration' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'teachers' => 'nullable|array',
            'teachers.*' => 'exists:users,id',
            'status' => 'required|in:active,inactive,draft',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date) : null;
        $endDate   = $request->end_date   ? \Carbon\Carbon::parse($request->end_date)   : null;
        $duration  = ($startDate && $endDate)
            ? $this->calcDuration($startDate, $endDate)
            : $request->duration;

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
            'fees' => $request->fees,
            'fees_type' => $request->fees_type,
            'past_month_fee' => $request->fees_type === 'monthly' ? ($request->past_month_fee ?? 0) : 0,
            'duration'   => $duration,
            'start_date' => $startDate?->toDateString(),
            'end_date'   => $endDate?->toDateString(),
            'status' => $request->status,
        ];

        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('courses', 'public');
            $data['thumbnail'] = $path;
        }

        $course->update($data);

        // Sync teachers
        if ($request->teachers) {
            $teachers = [];
            foreach ($request->teachers as $index => $teacherId) {
                $teachers[$teacherId] = ['is_primary' => $index === 0];
            }
            $course->teachers()->sync($teachers);
        } else {
            $course->teachers()->detach();
        }

        return redirect()->route('tenant.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    private function calcDuration(\Carbon\Carbon $start, \Carbon\Carbon $end): string
    {
        $months = (int) $start->diffInMonths($end);
        $days   = (int) $start->copy()->addMonths($months)->diffInDays($end);

        if ($months === 0) {
            return $days . ' day' . ($days != 1 ? 's' : '');
        }
        if ($days === 0) {
            return $months . ' month' . ($months != 1 ? 's' : '');
        }
        return $months . ' month' . ($months != 1 ? 's' : '') . ' ' . $days . ' day' . ($days != 1 ? 's' : '');
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', $course);
        $course->delete();
        return redirect()->route('tenant.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
