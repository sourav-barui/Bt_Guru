<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::where('tenant_id', Auth::user()->tenant_id)
            ->whereHas('roles', function ($q) { $q->where('name', 'teacher'); })
            ->latest()
            ->paginate(15);

        return view('tenant.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('tenant.teachers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $teacher = User::create([
            'tenant_id' => Auth::user()->tenant_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $teacher->assignRole('teacher');

        return redirect()->route('tenant.teachers.index')
            ->with('success', 'Teacher created successfully.');
    }

    public function show(User $teacher)
    {
        $teacher->load(['taughtCourses', 'taughtCourses.enrollments']);
        return view('tenant.teachers.show', compact('teacher'));
    }

    public function edit(User $teacher)
    {
        return view('tenant.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, User $teacher)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
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

        $teacher->update($data);

        return redirect()->route('tenant.teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    public function destroy(User $teacher)
    {
        $teacher->delete();
        return redirect()->route('tenant.teachers.index')
            ->with('success', 'Teacher deleted successfully.');
    }
}
