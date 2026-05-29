<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\SubjectContent;
use App\Models\SubjectNote;
use App\Models\LiveClass;
use App\Models\Exam;
use App\Models\User;
use Carbon\Carbon;

class MathCourseContentSeeder extends Seeder
{
    public function run(): void
    {
        // Get the Mathematics Mastery course
        $course = Course::where('title', 'Mathematics Mastery')->first();

        if (!$course) {
            $this->command->error('Mathematics Mastery course not found!');
            return;
        }

        // Get or create a subject
        $subject = Subject::firstOrCreate(
            ['course_id' => $course->id, 'name' => 'Algebra'],
            [
                'description' => 'Advanced Algebra Concepts',
                'sort_order' => 1
            ]
        );

        // Get or create a chapter
        $chapter = Chapter::firstOrCreate(
            ['subject_id' => $subject->id, 'title' => 'Linear Equations'],
            [
                'description' => 'Understanding linear equations',
                'sort_order' => 1
            ]
        );

        // Get or create a lesson
        $lesson = Lesson::firstOrCreate(
            ['chapter_id' => $chapter->id, 'title' => 'Introduction to Linear Equations'],
            [
                'description' => 'Basic concepts of linear equations',
                'sort_order' => 1
            ]
        );

        // Get a teacher user
        $teacher = User::role('teacher')->first() ?? User::first();

        // Past date (1 month ago)
        $pastDate = Carbon::now()->subMonth();

        // 1. Create Past Video Content
        $videoContent = SubjectContent::create([
            'subject_id' => $subject->id,
            'user_id' => $teacher->id,
            'title' => 'Linear Equations - Part 1 (Past Content)',
            'description' => 'Introduction to solving linear equations - Created 1 month ago',
            'video_url' => 'https://www.youtube.com/watch?v=example1',
            'created_at' => $pastDate,
            'updated_at' => $pastDate,
        ]);

        // 2. Create Past Note
        $note = SubjectNote::create([
            'subject_id' => $subject->id,
            'user_id' => $teacher->id,
            'title' => 'Linear Equations Notes (Past Content)',
            'file_url' => 'https://example.com/notes/linear-equations.pdf',
            'file_name' => 'linear-equations-notes.pdf',
            'created_at' => $pastDate,
            'updated_at' => $pastDate,
        ]);

        // 3. Create Past Live Class
        $liveClass = LiveClass::create([
            'tenant_id' => $course->tenant_id,
            'course_id' => $course->id,
            'subject_id' => $subject->id,
            'user_id' => $teacher->id,
            'title' => 'Live Class: Linear Equations Review (Past)',
            'description' => 'Interactive session on linear equations - Held 1 month ago',
            'platform' => 'zoom',
            'meeting_url' => 'https://zoom.us/j/example',
            'meeting_id' => '123456789',
            'meeting_password' => 'math123',
            'scheduled_at' => $pastDate,
            'duration_minutes' => 60,
            'status' => 'completed',
            'created_at' => $pastDate,
            'updated_at' => $pastDate,
        ]);

        // 4. Create Past Exam
        $exam = Exam::create([
            'tenant_id' => $course->tenant_id,
            'course_id' => $course->id,
            'subject_id' => $subject->id,
            'user_id' => $teacher->id,
            'title' => 'Linear Equations Quiz (Past Exam)',
            'description' => 'Test your knowledge on linear equations - Created 1 month ago',
            'total_questions' => 10,
            'total_marks' => 100,
            'duration_minutes' => 30,
            'passing_marks' => 40,
            'created_at' => $pastDate,
            'updated_at' => $pastDate,
        ]);

        // 5. Create Course-Level Live Class (Past)
        $courseLiveClass = LiveClass::create([
            'tenant_id' => $course->tenant_id,
            'course_id' => $course->id,
            'user_id' => $teacher->id,
            'title' => 'Course Orientation: Math Basics (Past)',
            'description' => 'Course introduction and basics - Held 1 month ago',
            'platform' => 'google_meet',
            'meeting_url' => 'https://meet.google.com/example',
            'scheduled_at' => $pastDate,
            'duration_minutes' => 45,
            'status' => 'completed',
            'created_at' => $pastDate,
            'updated_at' => $pastDate,
        ]);

        // 6. Create Course-Level Exam (Past)
        $courseExam = Exam::create([
            'tenant_id' => $course->tenant_id,
            'course_id' => $course->id,
            'user_id' => $teacher->id,
            'title' => 'Mathematics Diagnostic Test (Past)',
            'description' => 'Initial assessment test - Created 1 month ago',
            'total_questions' => 20,
            'total_marks' => 100,
            'duration_minutes' => 60,
            'passing_marks' => 50,
            'created_at' => $pastDate,
            'updated_at' => $pastDate,
        ]);

        // 7. Create Additional Past Videos
        for ($i = 2; $i <= 3; $i++) {
            SubjectContent::create([
                'subject_id' => $subject->id,
                'user_id' => $teacher->id,
                'title' => "Algebra Advanced Topic {$i} (Past Content)",
                'description' => "Advanced algebra concept {$i} - Created 1 month ago",
                'video_url' => "https://www.youtube.com/watch?v=example{$i}",
                'created_at' => $pastDate->copy()->addDays($i),
                'updated_at' => $pastDate->copy()->addDays($i),
            ]);
        }

        // 8. Create Additional Past Notes
        for ($i = 2; $i <= 3; $i++) {
            SubjectNote::create([
                'subject_id' => $subject->id,
                'user_id' => $teacher->id,
                'title' => "Study Material {$i} (Past Content)",
                'file_url' => "https://example.com/notes/material-{$i}.pdf",
                'file_name' => "study-material-{$i}.pdf",
                'created_at' => $pastDate->copy()->addDays($i + 5),
                'updated_at' => $pastDate->copy()->addDays($i + 5),
            ]);
        }

        $this->command->info('✓ Seeded past content for Mathematics Mastery course');
        $this->command->info('  - 4 Videos (1 month old)');
        $this->command->info('  - 3 Notes (1 month old)');
        $this->command->info('  - 2 Live Classes (1 month old)');
        $this->command->info('  - 2 Exams (1 month old)');
        $this->command->info('');
        $this->command->info('All content is dated 1 month ago to test the lock functionality.');
        $this->command->info('If monthly fee is not paid, these should show locked icons.');
    }
}
