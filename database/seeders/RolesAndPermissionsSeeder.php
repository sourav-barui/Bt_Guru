<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Super Admin permissions
            'manage_tenants',
            'manage_all_users',
            'view_analytics',
            'manage_plans',
            'manage_domains',
            'suspend_tenants',

            // Tenant Admin permissions
            'manage_courses',
            'manage_teachers',
            'manage_students',
            'manage_enrollments',
            'manage_fees',
            'manage_notices',
            'view_tenant_reports',
            'manage_tenant_settings',
            'approve_admissions',

            // Teacher permissions
            'view_assigned_courses',
            'view_course_students',
            'manage_attendance',
            'upload_materials',
            'view_teacher_dashboard',

            // Student permissions
            'view_enrolled_courses',
            'view_notices',
            'view_fee_status',
            'view_attendance',
            'view_student_dashboard',
            'access_course_materials',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions

        // Super Admin
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo([
            'manage_tenants',
            'manage_all_users',
            'view_analytics',
            'manage_plans',
            'manage_domains',
            'suspend_tenants',
        ]);

        // Tenant Admin
        $tenantAdminRole = Role::firstOrCreate(['name' => 'tenant_admin', 'guard_name' => 'web']);
        $tenantAdminRole->givePermissionTo([
            'manage_courses',
            'manage_teachers',
            'manage_students',
            'manage_enrollments',
            'manage_fees',
            'manage_notices',
            'view_tenant_reports',
            'manage_tenant_settings',
            'approve_admissions',
        ]);

        // Teacher
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $teacherRole->givePermissionTo([
            'view_assigned_courses',
            'view_course_students',
            'manage_attendance',
            'upload_materials',
            'view_teacher_dashboard',
        ]);

        // Student
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $studentRole->givePermissionTo([
            'view_enrolled_courses',
            'view_notices',
            'view_fee_status',
            'view_attendance',
            'view_student_dashboard',
            'access_course_materials',
        ]);
    }
}
