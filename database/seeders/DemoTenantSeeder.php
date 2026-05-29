<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Hash;

class DemoTenantSeeder extends Seeder
{
    public function run(): void
    {
        // Create or get demo tenant
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'future-academy'],
            [
                'coaching_name' => 'Future Academy',
                'subdomain' => 'futureacademy',
                'custom_domain' => null,
                'logo' => null,
                'email' => 'info@futureacademy.com',
                'phone' => '+91-9876543210',
                'address' => '123 Education Street, Knowledge City, India',
                'status' => 'active',
                'settings' => [
                    'theme_color' => '#3b82f6',
                    'timezone' => 'Asia/Kolkata',
                    'currency' => 'INR',
                ],
            ]
        );

        // Create Tenant Admin
        $tenantAdmin = User::firstOrCreate(
            ['email' => 'admin@futureacademy.com', 'tenant_id' => $tenant->id],
            [
                'name' => 'John Smith',
                'phone' => '+91-9876543211',
                'password' => Hash::make('TenantAdmin@123'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $tenantAdmin->assignRole('tenant_admin');

        // Create Teachers
        $teachers = [
            [
                'name' => 'Dr. Sarah Johnson',
                'email' => 'sarah@futureacademy.com',
                'phone' => '+91-9876543212',
            ],
            [
                'name' => 'Prof. Michael Chen',
                'email' => 'michael@futureacademy.com',
                'phone' => '+91-9876543213',
            ],
            [
                'name' => 'Ms. Priya Sharma',
                'email' => 'priya@futureacademy.com',
                'phone' => '+91-9876543214',
            ],
        ];

        $teacherUsers = [];
        foreach ($teachers as $teacherData) {
            $teacher = User::firstOrCreate(
                ['email' => $teacherData['email'], 'tenant_id' => $tenant->id],
                [
                    'name' => $teacherData['name'],
                    'phone' => $teacherData['phone'],
                    'password' => Hash::make('Teacher@123'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $teacher->assignRole('teacher');
            $teacherUsers[] = $teacher;
        }

        // Create Courses
        $courses = [
            [
                'title' => 'Mathematics Mastery',
                'description' => 'Comprehensive mathematics course covering algebra, geometry, and calculus.',
                'fees' => 15000,
                'duration' => '6 months',
                'teacher_index' => 0,
            ],
            [
                'title' => 'Physics Fundamentals',
                'description' => 'Learn the basics of physics including mechanics, thermodynamics, and electromagnetism.',
                'fees' => 18000,
                'duration' => '6 months',
                'teacher_index' => 1,
            ],
            [
                'title' => 'Chemistry Essentials',
                'description' => 'Organic and inorganic chemistry course for high school students.',
                'fees' => 16000,
                'duration' => '5 months',
                'teacher_index' => 2,
            ],
            [
                'title' => 'English Literature',
                'description' => 'Explore classic and modern English literature with expert guidance.',
                'fees' => 12000,
                'duration' => '4 months',
                'teacher_index' => 0,
            ],
        ];

        $courseModels = [];
        foreach ($courses as $index => $courseData) {
            $slug = \Illuminate\Support\Str::slug($courseData['title']);
            $course = Course::firstOrCreate(
                ['slug' => $slug, 'tenant_id' => $tenant->id],
                [
                    'title' => $courseData['title'],
                    'description' => $courseData['description'],
                    'fees' => $courseData['fees'],
                    'duration' => $courseData['duration'],
                    'status' => 'active',
                ]
            );

            // Assign teacher to course (if not already assigned)
            if (!$course->teachers()->where('teacher_id', $teacherUsers[$courseData['teacher_index']]->id)->exists()) {
                $course->teachers()->attach($teacherUsers[$courseData['teacher_index']]->id, [
                    'is_primary' => true,
                ]);
            }

            $courseModels[] = $course;
        }

        // Create Students
        $students = [
            ['name' => 'Rahul Kumar', 'email' => 'rahul@email.com', 'phone' => '+91-8888888881'],
            ['name' => 'Emma Wilson', 'email' => 'emma@email.com', 'phone' => '+91-8888888882'],
            ['name' => 'Amit Patel', 'email' => 'amit@email.com', 'phone' => '+91-8888888883'],
            ['name' => 'Sneha Gupta', 'email' => 'sneha@email.com', 'phone' => '+91-8888888884'],
            ['name' => 'David Lee', 'email' => 'david@email.com', 'phone' => '+91-8888888885'],
            ['name' => 'Priya Patel', 'email' => 'priya.s@email.com', 'phone' => '+91-8888888886'],
        ];

        $studentUsers = [];
        foreach ($students as $studentData) {
            $student = User::firstOrCreate(
                ['email' => $studentData['email'], 'tenant_id' => $tenant->id],
                [
                    'name' => $studentData['name'],
                    'phone' => $studentData['phone'],
                    'password' => Hash::make('Student@123'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
            $student->assignRole('student');
            $studentUsers[] = $student;
        }

        // Create Enrollments
        $enrollments = [
            ['student' => 0, 'course' => 0, 'status' => 'active', 'payment' => 'completed'],
            ['student' => 0, 'course' => 1, 'status' => 'active', 'payment' => 'completed'],
            ['student' => 1, 'course' => 0, 'status' => 'active', 'payment' => 'partial'],
            ['student' => 1, 'course' => 2, 'status' => 'pending', 'payment' => 'pending'],
            ['student' => 2, 'course' => 1, 'status' => 'active', 'payment' => 'completed'],
            ['student' => 3, 'course' => 2, 'status' => 'active', 'payment' => 'completed'],
            ['student' => 4, 'course' => 3, 'status' => 'approved', 'payment' => 'completed'],
            ['student' => 5, 'course' => 0, 'status' => 'pending', 'payment' => 'pending'],
        ];

        foreach ($enrollments as $enrollmentData) {
            $course = $courseModels[$enrollmentData['course']];
            $student = $studentUsers[$enrollmentData['student']];
            
            // Check if enrollment already exists
            $existingEnrollment = Enrollment::where('tenant_id', $tenant->id)
                ->where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->first();
            
            if ($existingEnrollment) {
                continue;
            }
            
            $feesPaid = match($enrollmentData['payment']) {
                'completed' => $course->fees,
                'partial' => $course->fees * 0.5,
                default => 0,
            };

            Enrollment::create([
                'tenant_id' => $tenant->id,
                'student_id' => $student->id,
                'course_id' => $course->id,
                'payment_status' => $enrollmentData['payment'],
                'enrollment_status' => $enrollmentData['status'],
                'fees_paid' => $feesPaid,
                'fees_total' => $course->fees,
                'enrolled_at' => in_array($enrollmentData['status'], ['active', 'completed']) ? now() : null,
                'approved_at' => in_array($enrollmentData['status'], ['active', 'approved', 'completed']) ? now() : null,
                'approved_by' => $tenantAdmin->id,
            ]);
        }

        $this->command->info('Demo tenant created successfully!');
        $this->command->info('Subdomain: futureacademy.btguru.test');
        $this->command->info('Tenant Admin: admin@futureacademy.com / TenantAdmin@123');
        $this->command->info('Teachers: sarah@futureacademy.com, michael@futureacademy.com, priya@futureacademy.com / Teacher@123');
        $this->command->info('Students: rahul@email.com, emma@email.com, etc. / Student@123');
    }
}
