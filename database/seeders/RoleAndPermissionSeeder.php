<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'master.view', 'master.create', 'master.update', 'master.delete',
            'users.view', 'users.create', 'users.update', 'users.delete',
            'roles.manage',
            'courses.view', 'courses.create', 'courses.update', 'courses.delete',
            'offerings.view', 'offering.manage', 'period.manage',
            'krs.manage', 'krs.approve',
            'grades.view', 'grades.manage',
            'attendance.view', 'attendance.manage',
            'material.view', 'material.manage',
            'assignment.manage', 'submission.manage',
            'thesis.view', 'thesis.manage', 'internship.view', 'internship.manage',
            'lecturer-attendance.view', 'lecturer-attendance.manage',
            'advisor.manage', 'ai-advisor.use', 'report.view',
            'announcement.view', 'announcement.manage',
            'schedule.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $rolePermissions = [
            'super-admin' => $permissions,
            'kaprodi' => [
                'dashboard.view',
                'courses.view', 'courses.create', 'courses.update', 'courses.delete',
                'offerings.view',
                'grades.view',
                'thesis.view', 'internship.view',
                'lecturer-attendance.view',
                'announcement.view',
            ],
            'dosen' => [
                'dashboard.view',
                'offerings.view',
                'grades.view', 'grades.manage',
                'attendance.view', 'attendance.manage',
                'material.view', 'material.manage',
                'assignment.manage',
                'krs.approve',
                'thesis.view', 'internship.view',
                'lecturer-attendance.view', 'lecturer-attendance.manage',
                'announcement.view',
            ],
            'mahasiswa' => [
                'dashboard.view',
                'offerings.view',
                'krs.manage',
                'material.view',
                'submission.manage',
                'grades.view',
                'thesis.view', 'internship.view',
                'ai-advisor.use',
                'announcement.view',
                'schedule.view',
            ],
            'pimpinan' => [
                'dashboard.view',
                'report.view',
                'grades.view',
                'attendance.view',
                'thesis.view', 'internship.view',
                'lecturer-attendance.view',
                'announcement.view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            /** @var Role $role */
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($perms);
        }
    }
}
