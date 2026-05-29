<?php

namespace App\Services;

use App\Models\StudentNotification;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Config;

class NotificationService
{
    /**
     * Dispatch a notification to one or many students.
     * $users: Collection<User> or single User
     */
    public function send(
        Tenant $tenant,
        $users,
        string $type,
        string $title,
        string $body = '',
        string $icon = 'bell',
        string $url = '',
        bool $sendEmail = true
    ): void {
        $users = $users instanceof Collection ? $users : collect([$users]);

        $rows = $users->map(fn(User $u) => [
            'tenant_id'  => $tenant->id,
            'user_id'    => $u->id,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'icon'       => $icon,
            'url'        => $url,
            'is_read'    => false,
            'read_at'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ])->toArray();

        StudentNotification::insert($rows);

        if ($sendEmail) {
            $this->sendEmails($tenant, $users, $title, $body, $url);
        }
    }

    private function sendEmails(Tenant $tenant, Collection $users, string $title, string $body, string $url): void
    {
        $s = $tenant->settings ?? [];

        // Only send if tenant has configured their own SMTP
        if (empty($s['mail_host']) || empty($s['mail_username'])) {
            return;
        }

        // Temporarily override mail config with tenant settings
        Config::set('mail.default', $s['mail_driver'] ?? 'smtp');
        Config::set('mail.mailers.smtp.host', $s['mail_host']);
        Config::set('mail.mailers.smtp.port', $s['mail_port'] ?? 587);
        Config::set('mail.mailers.smtp.username', $s['mail_username']);
        Config::set('mail.mailers.smtp.password', $s['mail_password'] ?? '');
        Config::set('mail.mailers.smtp.encryption', $s['mail_encryption'] ?? 'tls');
        Config::set('mail.from.address', $s['mail_from_address'] ?? $s['mail_username']);
        Config::set('mail.from.name', $s['mail_from_name'] ?? $tenant->coaching_name);

        $coachingName = $tenant->coaching_name;
        $fromAddress  = $s['mail_from_address'] ?? $s['mail_username'];
        $fromName     = $s['mail_from_name'] ?? $coachingName;

        foreach ($users as $user) {
            if (empty($user->email)) continue;

            try {
                Mail::send([], [], function (Message $msg) use ($user, $title, $body, $url, $coachingName, $fromAddress, $fromName) {
                    $msg->to($user->email, $user->name)
                        ->from($fromAddress, $fromName)
                        ->subject($title)
                        ->html(view('emails.student_notification', compact('user', 'title', 'body', 'url', 'coachingName'))->render());
                });
            } catch (\Throwable $e) {
                \Log::warning("Notification email failed for user {$user->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Helpers for specific event types
     */
    public function noticePosted(Tenant $tenant, \App\Models\Notice $notice): void
    {
        $students = User::where('tenant_id', $tenant->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->get();

        $this->send(
            $tenant, $students,
            type: 'notice',
            title: $notice->title,
            body: \Illuminate\Support\Str::limit(strip_tags($notice->content ?? ''), 120),
            icon: $notice->type === 'urgent' ? 'bell' : 'bell',
            url: '/student/dashboard',
            sendEmail: true
        );
    }

    public function liveClassScheduled(Tenant $tenant, \App\Models\LiveClass $liveClass): void
    {
        $students = User::where('tenant_id', $tenant->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->whereHas('enrollments', fn($q) => $q->where('course_id', $liveClass->course_id)->where('enrollment_status', 'active'))
            ->get();

        $scheduled = $liveClass->scheduled_at->format('d M, h:i A');
        $this->send(
            $tenant, $students,
            type: 'live_class',
            title: "Live Class: {$liveClass->title}",
            body: "Scheduled on {$scheduled} — {$liveClass->course->title}",
            icon: 'live',
            url: '/student/live-classes',
            sendEmail: true
        );
    }

    public function liveClassStarted(Tenant $tenant, \App\Models\LiveClass $liveClass): void
    {
        $students = User::where('tenant_id', $tenant->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->whereHas('enrollments', fn($q) => $q->where('course_id', $liveClass->course_id)->where('enrollment_status', 'active'))
            ->get();

        $this->send(
            $tenant, $students,
            type: 'live_class',
            title: "🔴 LIVE NOW: {$liveClass->title}",
            body: "Your live class has started! Click to join now — {$liveClass->course->title}",
            icon: 'live',
            url: $liveClass->meeting_url ?? '/student/live-classes',
            sendEmail: true
        );
    }

    public function examPublished(Tenant $tenant, \App\Models\Exam $exam): void
    {
        $students = User::where('tenant_id', $tenant->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->whereHas('enrollments', fn($q) => $q->where('course_id', $exam->course_id)->where('enrollment_status', 'active'))
            ->get();

        $this->send(
            $tenant, $students,
            type: 'exam',
            title: "New Exam: {$exam->title}",
            body: "A new exam has been published in {$exam->course->title}.",
            icon: 'exam',
            url: '/student/exams',
            sendEmail: true
        );
    }

    public function lessonAdded(Tenant $tenant, \App\Models\Lesson $lesson): void
    {
        // Load relationships to get course
        $lesson->load(['chapter.subject.curriculum.course']);

        $course = $lesson->chapter?->subject?->curriculum?->course;

        if (!$course) {
            \Log::warning('Cannot send lesson notification: course not found for lesson ' . $lesson->id);
            return;
        }

        // Get only students enrolled in this course
        $students = User::where('tenant_id', $tenant->id)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->whereHas('enrollments', fn($q) => $q->where('course_id', $course->id)->where('enrollment_status', 'active'))
            ->get();

        if ($students->isEmpty()) {
            return; // No enrolled students to notify
        }

        $this->send(
            $tenant, $students,
            type: 'video',
            title: "New Video: {$lesson->title}",
            body: "A new lesson has been added to {$course->title}.",
            icon: 'video',
            url: '/student/courses',
            sendEmail: false
        );
    }

    public function enrollmentApproved(Tenant $tenant, User $student, \App\Models\Course $course): void
    {
        $this->send(
            $tenant, $student,
            type: 'course',
            title: "Enrollment Approved: {$course->title}",
            body: "Your enrollment in {$course->title} has been approved. Start learning now!",
            icon: 'course',
            url: '/student/courses',
            sendEmail: true
        );
    }

    public function paymentVerified(Tenant $tenant, User $student, string $courseName): void
    {
        $this->send(
            $tenant, $student,
            type: 'payment',
            title: 'Payment Verified',
            body: "Your payment for {$courseName} has been verified.",
            icon: 'payment',
            url: '/student/payments',
            sendEmail: true
        );
    }
}
