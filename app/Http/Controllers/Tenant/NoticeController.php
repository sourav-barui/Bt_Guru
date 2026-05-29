<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notice;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\NotificationService;

class NoticeController extends Controller
{
    public function index()
    {
        $notices = Notice::where('tenant_id', Auth::user()->tenant_id)
            ->latest()
            ->with(['course'])
            ->paginate(15);

        return view('tenant.notices.index', compact('notices'));
    }

    public function create()
    {
        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->where('status', 'active')
            ->get();
        return view('tenant.notices.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,important,urgent',
            'audience' => 'required|in:all,students,teachers',
            'course_id' => 'nullable|exists:courses,id',
            'publish_at' => 'nullable|date',
            'expire_at' => 'nullable|date|after:publish_at',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $notice = Notice::create([
            'tenant_id' => Auth::user()->tenant_id,
            'created_by' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'audience' => $request->audience,
            'course_id' => $request->course_id,
            'publish_at' => $request->publish_at,
            'expire_at' => $request->expire_at,
            'is_active' => true,
        ]);

        try {
            (new NotificationService())->noticePosted(Auth::user()->tenant, $notice);
        } catch (\Throwable $e) {
            \Log::warning('Notice notification failed: ' . $e->getMessage());
        }

        return redirect()->route('tenant.notices.index')
            ->with('success', 'Notice created successfully.');
    }

    public function show(Notice $notice)
    {
        return view('tenant.notices.show', compact('notice'));
    }

    public function edit(Notice $notice)
    {
        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->where('status', 'active')
            ->get();
        return view('tenant.notices.edit', compact('notice', 'courses'));
    }

    public function update(Request $request, Notice $notice)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:general,important,urgent',
            'audience' => 'required|in:all,students,teachers',
            'course_id' => 'nullable|exists:courses,id',
            'publish_at' => 'nullable|date',
            'expire_at' => 'nullable|date|after:publish_at',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $notice->update([
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'audience' => $request->audience,
            'course_id' => $request->course_id,
            'publish_at' => $request->publish_at,
            'expire_at' => $request->expire_at,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('tenant.notices.index')
            ->with('success', 'Notice updated successfully.');
    }

    public function destroy(Notice $notice)
    {
        $notice->delete();
        return redirect()->route('tenant.notices.index')
            ->with('success', 'Notice deleted successfully.');
    }
}
