<?php

namespace App\Providers;

use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\CourseMaterial;
use App\Models\Grade;
use App\Models\Internship;
use App\Models\LecturerAttendance;
use App\Models\Report;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\Thesis;
use App\Models\ThesisLog;
use App\Policies\AcademicPeriodPolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\AssignmentSubmissionPolicy;
use App\Policies\GradePolicy;
use App\Policies\InternshipPolicy;
use App\Policies\LecturerAttendancePolicy;
use App\Policies\MaterialPolicy;
use App\Policies\ReportPolicy;
use App\Policies\StudentPolicy;
use App\Policies\StudyPlanDetailPolicy;
use App\Policies\StudyPlanPolicy;
use App\Policies\ThesisLogPolicy;
use App\Policies\ThesisPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super-admin memiliki akses penuh ke seluruh ability.
        Gate::before(function ($user) {
            return $user->hasRole('super-admin') ? true : null;
        });

        // Satu policy untuk entitas periode akademik (tahun ajaran & semester).
        Gate::policy(AcademicYear::class, AcademicPeriodPolicy::class);
        Gate::policy(Semester::class, AcademicPeriodPolicy::class);
        Gate::policy(CourseMaterial::class, MaterialPolicy::class);
        Gate::policy(StudyPlan::class, StudyPlanPolicy::class);
        Gate::policy(StudyPlanDetail::class, StudyPlanDetailPolicy::class);
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(AssignmentSubmission::class, AssignmentSubmissionPolicy::class);
        Gate::policy(Grade::class, GradePolicy::class);
        Gate::policy(Thesis::class, ThesisPolicy::class);
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(ThesisLog::class, ThesisLogPolicy::class);
        Gate::policy(LecturerAttendance::class, LecturerAttendancePolicy::class);
        Gate::policy(Internship::class, InternshipPolicy::class);
        Gate::policy(Report::class, ReportPolicy::class);
    }
}
