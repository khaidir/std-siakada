<?php

namespace App\Providers;

use App\Repositories\Contracts\AcademicPeriodRepository;
use App\Repositories\Contracts\AnnouncementRepository;
use App\Repositories\Contracts\AssignmentRepository;
use App\Repositories\Contracts\AttendanceRepository;
use App\Repositories\Contracts\ClassroomRepository;
use App\Repositories\Contracts\CourseOfferingRepository;
use App\Repositories\Contracts\CourseRepository;
use App\Repositories\Contracts\DashboardRepository;
use App\Repositories\Contracts\FacultyRepository;
use App\Repositories\Contracts\GradeRepository;
use App\Repositories\Contracts\LecturerRepository;
use App\Repositories\Contracts\MaterialRepository;
use App\Repositories\Contracts\StudentRepository;
use App\Repositories\Contracts\StudyPlanDetailRepository;
use App\Repositories\Contracts\StudyPlanRepository;
use App\Repositories\Contracts\StudyProgramRepository;
use App\Repositories\Contracts\SubmissionRepository;
use App\Repositories\Contracts\UserRepository;
use App\Repositories\Eloquent\EloquentAcademicPeriodRepository;
use App\Repositories\Eloquent\EloquentAnnouncementRepository;
use App\Repositories\Eloquent\AttendanceRepository as EloquentAttendanceRepository;
use App\Repositories\Eloquent\EloquentClassroomRepository;
use App\Repositories\Eloquent\CourseOfferingRepository as EloquentCourseOfferingRepository;
use App\Repositories\Eloquent\DashboardRepository as EloquentDashboardRepository;
use App\Repositories\Eloquent\EloquentAssignmentRepository;
use App\Repositories\Eloquent\EloquentCourseRepository;
use App\Repositories\Eloquent\EloquentFacultyRepository;
use App\Repositories\Eloquent\EloquentLecturerRepository;
use App\Repositories\Eloquent\EloquentMaterialRepository;
use App\Repositories\Eloquent\EloquentStudyPlanRepository;
use App\Repositories\Eloquent\EloquentStudyProgramRepository;
use App\Repositories\Eloquent\EloquentSubmissionRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use App\Repositories\Eloquent\GradeRepository as EloquentGradeRepository;
use App\Repositories\Eloquent\StudentRepository as EloquentStudentRepository;
use App\Repositories\Eloquent\StudyPlanDetailRepository as EloquentStudyPlanDetailRepository;
use App\Repositories\Contracts\LecturerAttendanceRepository;
use App\Repositories\Contracts\ReportRepository;
use App\Repositories\Contracts\ThesisRepository;
use App\Repositories\Contracts\InternshipRepository;
use App\Repositories\Eloquent\EloquentLecturerAttendanceRepository;
use App\Repositories\Eloquent\EloquentReportRepository;
use App\Repositories\Eloquent\EloquentThesisRepository;
use App\Repositories\Eloquent\EloquentInternshipRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(DashboardRepository::class, EloquentDashboardRepository::class);
        $this->app->bind(GradeRepository::class, EloquentGradeRepository::class);
        $this->app->bind(StudyPlanDetailRepository::class, EloquentStudyPlanDetailRepository::class);
        $this->app->bind(StudentRepository::class, EloquentStudentRepository::class);
        $this->app->bind(CourseOfferingRepository::class, EloquentCourseOfferingRepository::class);
        $this->app->bind(AttendanceRepository::class, EloquentAttendanceRepository::class);
        $this->app->bind(MaterialRepository::class, EloquentMaterialRepository::class);
        $this->app->bind(StudyPlanRepository::class, EloquentStudyPlanRepository::class);
        $this->app->bind(AssignmentRepository::class, EloquentAssignmentRepository::class);
        $this->app->bind(SubmissionRepository::class, EloquentSubmissionRepository::class);
        $this->app->bind(CourseRepository::class, EloquentCourseRepository::class);
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(LecturerRepository::class, EloquentLecturerRepository::class);
        $this->app->bind(FacultyRepository::class, EloquentFacultyRepository::class);
        $this->app->bind(StudyProgramRepository::class, EloquentStudyProgramRepository::class);
        $this->app->bind(ClassroomRepository::class, EloquentClassroomRepository::class);
        $this->app->bind(AcademicPeriodRepository::class, EloquentAcademicPeriodRepository::class);
        $this->app->bind(AnnouncementRepository::class, EloquentAnnouncementRepository::class);
        $this->app->bind(ThesisRepository::class, EloquentThesisRepository::class);
        $this->app->bind(InternshipRepository::class, EloquentInternshipRepository::class);
        $this->app->bind(LecturerAttendanceRepository::class, EloquentLecturerAttendanceRepository::class);
        $this->app->bind(ReportRepository::class, EloquentReportRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
