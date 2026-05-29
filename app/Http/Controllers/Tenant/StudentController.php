<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index()
    {
        $students = User::where('tenant_id', Auth::user()->tenant_id)
            ->whereHas('roles', function ($q) { $q->where('name', 'student'); })
            ->latest()
            ->paginate(15);

        return view('tenant.students.index', compact('students'));
    }

    public function create()
    {
        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->where('status', 'active')
            ->get();
        return view('tenant.students.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'course_id' => 'nullable|exists:courses,id',
            'enrollment_status' => 'nullable|in:pending,approved,active',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $student = User::create([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $student->assignRole('student');

        // Create enrollment if course selected
        if ($request->course_id) {
            $course = Course::find($request->course_id);
            $enrollmentStatus = $request->enrollment_status ?? 'pending';
            
            Enrollment::create([
                'tenant_id' => Auth::user()->tenant_id,
                'student_id' => $student->id,
                'course_id' => $course->id,
                'payment_status' => 'pending',
                'enrollment_status' => $enrollmentStatus,
                'fees_total' => $course->fees,
                'enrolled_at' => $enrollmentStatus === 'active' ? now() : null,
                'approved_at' => in_array($enrollmentStatus, ['approved', 'active']) ? now() : null,
                'approved_by' => Auth::id(),
            ]);
        }

        return redirect()->route('tenant.students.index')
            ->with('success', 'Student created successfully.');
    }

    public function show(User $student)
    {
        $student->load(['enrollments.course']);
        return view('tenant.students.show', compact('student'));
    }

    public function edit(User $student)
    {
        return view('tenant.students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $student->update($data);

        return redirect()->route('tenant.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(User $student)
    {
        $student->delete();
        return redirect()->route('tenant.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function logoutFromAllDevices(User $student)
    {
        // Clear all sessions for this student (this updates DB directly and refreshes model)
        $student->logoutFromAllDevices();

        return redirect()->route('tenant.students.index')
            ->with('success', 'Student logged out from all devices successfully. They can now login again.');
    }
}
