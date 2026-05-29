<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = StudentNotification::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        StudentNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('student.notifications', compact('notifications'));
    }

    public function unreadCount()
    {
        $count = StudentNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markRead(StudentNotification $notification)
    {
        if ($notification->user_id === Auth::id()) {
            $notification->markRead();
        }
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        StudentNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function recent()
    {
        $notifications = StudentNotification::where('user_id', Auth::id())
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'body'       => $n->body,
                'icon'       => $n->icon,
                'icon_class' => $n->icon_class,
                'icon_svg'   => $n->icon_svg,
                'url'        => $n->url,
                'is_read'    => $n->is_read,
                'time'       => $n->created_at->diffForHumans(),
            ]);

        return response()->json(['notifications' => $notifications]);
    }
}
