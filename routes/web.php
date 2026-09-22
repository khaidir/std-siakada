<?php

use App\Http\Controllers\Admin\AcademicPeriodController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\CourseOfferingController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\InternshipController as AdminInternshipController;
use App\Http\Controllers\Admin\KrsMonitoringController;
use App\Http\Controllers\Admin\LecturerAttendanceController as AdminLecturerAttendanceController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\StudyProgramController;
use App\Http\Controllers\Admin\ThesisController as AdminThesisController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dosen\AssignmentController;
use App\Http\Controllers\Dosen\AttendanceController;
use App\Http\Controllers\Dosen\GradeController;
use App\Http\Controllers\Dosen\KrsApprovalController;
use App\Http\Controllers\Dosen\LecturerAttendanceController;
use App\Http\Controllers\Dosen\MaterialController;
use App\Http\Controllers\Dosen\SubmissionGradeController;
use App\Http\Controllers\Dosen\ThesisController;
use App\Http\Controllers\Kaprodi\CourseController as KaprodiCourseController;
use App\Http\Controllers\Kaprodi\LecturerAttendanceController as KaprodiLecturerAttendanceController;
use App\Http\Controllers\Mahasiswa\AssignmentController as MahasiswaAssignmentController;
use App\Http\Controllers\Mahasiswa\GradeController as MahasiswaGradeController;
use App\Http\Controllers\Mahasiswa\InternshipController;
use App\Http\Controllers\Mahasiswa\KhsController;
use App\Http\Controllers\Mahasiswa\KrsController;
use App\Http\Controllers\Mahasiswa\MaterialController as MahasiswaMaterialController;
use App\Http\Controllers\Mahasiswa\PresenceController;
use App\Http\Controllers\Mahasiswa\ProfileController;
use App\Http\Controllers\Mahasiswa\ScheduleController;
use App\Http\Controllers\Mahasiswa\TranskripController;
use App\Http\Controllers\Pimpinan\ReportController as PimpinanReportController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('auth/Login');
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware(['auth', 'role:super-admin', 'permission:dashboard.view'])->group(function () {
    Route::get('/admin/dashboard', DashboardController::class)->name('admin.dashboard');
});

Route::middleware(['auth', 'role:super-admin', 'permission:users.view'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});

Route::middleware(['auth', 'role:super-admin', 'permission:roles.manage'])->group(function () {
    Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::post('/admin/roles/permissions', [RoleController::class, 'updatePermissions'])->name('admin.roles.update-permissions');
});

Route::middleware(['auth', 'role:super-admin', 'permission:master.view'])->group(function () {
    // Fakultas
    Route::get('/admin/faculties', [FacultyController::class, 'index'])->name('admin.faculties.index');
    Route::post('/admin/faculties', [FacultyController::class, 'store'])->name('admin.faculties.store');
    Route::put('/admin/faculties/{id}', [FacultyController::class, 'update'])->name('admin.faculties.update');
    Route::delete('/admin/faculties/{id}', [FacultyController::class, 'destroy'])->name('admin.faculties.destroy');

    // Program Studi
    Route::get('/admin/study-programs', [StudyProgramController::class, 'index'])->name('admin.study-programs.index');
    Route::post('/admin/study-programs', [StudyProgramController::class, 'store'])->name('admin.study-programs.store');
    Route::put('/admin/study-programs/{id}', [StudyProgramController::class, 'update'])->name('admin.study-programs.update');
    Route::delete('/admin/study-programs/{id}', [StudyProgramController::class, 'destroy'])->name('admin.study-programs.destroy');

    // Mata Kuliah (admin — semua prodi)
    Route::get('/admin/courses', [AdminCourseController::class, 'index'])->name('admin.courses.index');
    Route::post('/admin/courses', [AdminCourseController::class, 'store'])->name('admin.courses.store');
    Route::put('/admin/courses/{id}', [AdminCourseController::class, 'update'])->name('admin.courses.update');
    Route::delete('/admin/courses/{id}', [AdminCourseController::class, 'destroy'])->name('admin.courses.destroy');

    // Ruangan
    Route::get('/admin/classrooms', [ClassroomController::class, 'index'])->name('admin.classrooms.index');
    Route::post('/admin/classrooms', [ClassroomController::class, 'store'])->name('admin.classrooms.store');
    Route::put('/admin/classrooms/{id}', [ClassroomController::class, 'update'])->name('admin.classrooms.update');
    Route::delete('/admin/classrooms/{id}', [ClassroomController::class, 'destroy'])->name('admin.classrooms.destroy');
});

Route::middleware(['auth', 'role:super-admin', 'permission:offering.manage'])->group(function () {
    Route::get('/admin/course-offerings', [CourseOfferingController::class, 'index'])->name('admin.course-offerings.index');
    Route::post('/admin/course-offerings', [CourseOfferingController::class, 'store'])->name('admin.course-offerings.store');
    Route::put('/admin/course-offerings/{id}', [CourseOfferingController::class, 'update'])->name('admin.course-offerings.update');
    Route::delete('/admin/course-offerings/{id}', [CourseOfferingController::class, 'destroy'])->name('admin.course-offerings.destroy');
});

Route::middleware(['auth', 'role:super-admin', 'permission:period.manage'])->group(function () {
    Route::get('/admin/periods', [AcademicPeriodController::class, 'index'])->name('admin.periods.index');
    Route::post('/admin/periods/academic-year', [AcademicPeriodController::class, 'storeAcademicYear'])->name('admin.periods.academic-year.store');
    Route::put('/admin/periods/academic-year/{id}', [AcademicPeriodController::class, 'updateAcademicYear'])->name('admin.periods.academic-year.update');
    Route::delete('/admin/periods/academic-year/{id}', [AcademicPeriodController::class, 'destroyAcademicYear'])->name('admin.periods.academic-year.destroy');
    Route::post('/admin/periods/semester', [AcademicPeriodController::class, 'storeSemester'])->name('admin.periods.semester.store');
    Route::put('/admin/periods/semester/{id}', [AcademicPeriodController::class, 'updateSemester'])->name('admin.periods.semester.update');
    Route::delete('/admin/periods/semester/{id}', [AcademicPeriodController::class, 'destroySemester'])->name('admin.periods.semester.destroy');
});

Route::middleware(['auth', 'role:super-admin', 'permission:announcement.manage'])->group(function () {
    Route::get('/admin/announcements', [AnnouncementController::class, 'index'])->name('admin.announcements.index');
    Route::post('/admin/announcements', [AnnouncementController::class, 'store'])->name('admin.announcements.store');
    Route::put('/admin/announcements/{id}', [AnnouncementController::class, 'update'])->name('admin.announcements.update');
    Route::delete('/admin/announcements/{id}', [AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');
});

Route::middleware(['auth', 'role:super-admin', 'permission:krs.manage'])->group(function () {
    Route::get('/admin/krs-monitoring', [KrsMonitoringController::class, 'index'])->name('admin.krs-monitoring.index');
    Route::get('/admin/krs-monitoring/{id}', [KrsMonitoringController::class, 'show'])->name('admin.krs-monitoring.show');
});

Route::middleware(['auth', 'role:super-admin', 'permission:thesis.manage'])->group(function () {
    Route::get('/admin/skripsi', [AdminThesisController::class, 'index'])->name('admin.skripsi.index');
    Route::post('/admin/skripsi/{id}/assign', [AdminThesisController::class, 'assign'])->name('admin.skripsi.assign');
    Route::put('/admin/skripsi/{id}/status', [AdminThesisController::class, 'updateStatus'])->name('admin.skripsi.update-status');
});

Route::middleware(['auth', 'role:super-admin', 'permission:lecturer-attendance.view'])->group(function () {
    Route::get('/admin/kehadiran-dosen', [AdminLecturerAttendanceController::class, 'index'])->name('admin.kehadiran.index');
    Route::get('/admin/kehadiran-dosen/{lecturerId}', [AdminLecturerAttendanceController::class, 'show'])->name('admin.kehadiran.show');
});

Route::middleware(['auth', 'role:super-admin', 'permission:internship.manage'])->group(function () {
    Route::get('/admin/kp', [AdminInternshipController::class, 'index'])->name('admin.kp.index');
    Route::post('/admin/kp/{id}/assign', [AdminInternshipController::class, 'assign'])->name('admin.kp.assign');
    Route::put('/admin/kp/{id}/status', [AdminInternshipController::class, 'updateStatus'])->name('admin.kp.update-status');
});

Route::middleware(['auth', 'role:kaprodi', 'permission:dashboard.view'])->group(function () {
    Route::get('/kaprodi/dashboard', DashboardController::class)->name('kaprodi.dashboard');
});

Route::middleware(['auth', 'role:super-admin|kaprodi|pimpinan', 'permission:report.view'])->group(function () {
    Route::get('/pimpinan/laporan', [PimpinanReportController::class, 'index'])->name('pimpinan.laporan');
});

Route::middleware(['auth', 'role:kaprodi', 'permission:courses.view'])->group(function () {
    Route::get('/kaprodi/courses', [KaprodiCourseController::class, 'index'])->name('kaprodi.courses.index');
    Route::post('/kaprodi/courses', [KaprodiCourseController::class, 'store'])->name('kaprodi.courses.store');
    Route::put('/kaprodi/courses/{id}', [KaprodiCourseController::class, 'update'])->name('kaprodi.courses.update');
    Route::delete('/kaprodi/courses/{id}', [KaprodiCourseController::class, 'destroy'])->name('kaprodi.courses.destroy');
});

Route::middleware(['auth', 'role:kaprodi', 'permission:lecturer-attendance.view'])->group(function () {
    Route::get('/kaprodi/kehadiran', [KaprodiLecturerAttendanceController::class, 'index'])->name('kaprodi.kehadiran.index');
    Route::get('/kaprodi/kehadiran/{lecturerId}', [KaprodiLecturerAttendanceController::class, 'show'])->name('kaprodi.kehadiran.show');
});

Route::middleware(['auth', 'role:dosen', 'permission:dashboard.view'])->group(function () {
    Route::get('/dosen/dashboard', DashboardController::class)->name('dosen.dashboard');
});

Route::middleware(['auth', 'role:dosen', 'permission:grades.manage'])->group(function () {
    Route::get('/dosen/nilai', [GradeController::class, 'index'])->name('dosen.nilai.index');
    Route::post('/dosen/nilai', [GradeController::class, 'store'])->name('dosen.nilai.store');
});

Route::middleware(['auth', 'role:dosen', 'permission:attendance.manage'])->group(function () {
    Route::get('/dosen/presensi', [AttendanceController::class, 'index'])->name('dosen.presensi.index');
    Route::post('/dosen/presensi', [AttendanceController::class, 'store'])->name('dosen.presensi.store');
});

Route::middleware(['auth', 'role:dosen', 'permission:material.manage'])->group(function () {
    Route::get('/dosen/materi', [MaterialController::class, 'index'])->name('dosen.materi.index');
    Route::post('/dosen/materi', [MaterialController::class, 'store'])->name('dosen.materi.store');
    Route::put('/dosen/materi/{id}', [MaterialController::class, 'update'])->name('dosen.materi.update');
    Route::delete('/dosen/materi/{id}', [MaterialController::class, 'destroy'])->name('dosen.materi.destroy');
});

Route::middleware(['auth', 'role:dosen', 'permission:assignment.manage'])->group(function () {
    Route::get('/dosen/tugas', [AssignmentController::class, 'index'])->name('dosen.tugas.index');
    Route::post('/dosen/tugas', [AssignmentController::class, 'store'])->name('dosen.tugas.store');
    Route::put('/dosen/tugas/{id}', [AssignmentController::class, 'update'])->name('dosen.tugas.update');
    Route::delete('/dosen/tugas/{id}', [AssignmentController::class, 'destroy'])->name('dosen.tugas.destroy');
    Route::get('/dosen/tugas/{assignment}/nilai', [SubmissionGradeController::class, 'index'])->name('dosen.tugas.nilai.index');
    Route::post('/dosen/tugas/{assignment}/nilai', [SubmissionGradeController::class, 'store'])->name('dosen.tugas.nilai.store');
});

Route::middleware(['auth', 'role:dosen', 'permission:lecturer-attendance.manage'])->group(function () {
    Route::get('/dosen/kehadiran', [LecturerAttendanceController::class, 'index'])->name('dosen.kehadiran.index');
    Route::post('/dosen/kehadiran/check-in', [LecturerAttendanceController::class, 'checkIn'])->name('dosen.kehadiran.check-in');
    Route::post('/dosen/kehadiran/{id}/check-out', [LecturerAttendanceController::class, 'checkOut'])->name('dosen.kehadiran.check-out');
});

Route::middleware(['auth', 'role:dosen', 'permission:krs.approve'])->group(function () {
    Route::get('/dosen/bimbingan-pa', [KrsApprovalController::class, 'index'])->name('dosen.bimbingan-pa.index');
    Route::get('/dosen/bimbingan-pa/{id}', [KrsApprovalController::class, 'show'])->name('dosen.bimbingan-pa.show');
    Route::post('/dosen/bimbingan-pa/{id}/approve', [KrsApprovalController::class, 'approve'])->name('dosen.bimbingan-pa.approve');
    Route::post('/dosen/bimbingan-pa/{id}/reject', [KrsApprovalController::class, 'reject'])->name('dosen.bimbingan-pa.reject');
});

Route::middleware(['auth', 'role:dosen', 'permission:thesis.view'])->group(function () {
    Route::get('/dosen/bimbingan-skripsi', [ThesisController::class, 'index'])->name('dosen.bimbingan-skripsi.index');
    Route::get('/dosen/bimbingan-skripsi/{id}', [ThesisController::class, 'show'])->name('dosen.bimbingan-skripsi.show');
    Route::put('/dosen/bimbingan-skripsi/{id}/status', [ThesisController::class, 'updateStatus'])->name('dosen.bimbingan-skripsi.update-status');
    Route::post('/dosen/bimbingan-skripsi/{id}/approve-log', [ThesisController::class, 'approveLog'])->name('dosen.bimbingan-skripsi.approve-log');
});

Route::middleware(['auth', 'role:mahasiswa', 'permission:dashboard.view'])->group(function () {
    Route::get('/mahasiswa/dashboard', DashboardController::class)->name('mahasiswa.dashboard');
});

Route::middleware(['auth', 'role:mahasiswa', 'permission:krs.manage'])->group(function () {
    Route::get('/mahasiswa/krs', [KrsController::class, 'index'])->name('mahasiswa.krs.index');
    Route::post('/mahasiswa/krs', [KrsController::class, 'store'])->name('mahasiswa.krs.store');
    Route::delete('/mahasiswa/krs/{id}', [KrsController::class, 'destroy'])->name('mahasiswa.krs.destroy');
    Route::post('/mahasiswa/krs/submit', [KrsController::class, 'submit'])->name('mahasiswa.krs.submit');
});

Route::middleware(['auth', 'role:mahasiswa', 'permission:material.view'])->group(function () {
    Route::get('/mahasiswa/materi', [MahasiswaMaterialController::class, 'index'])->name('mahasiswa.materi.index');
});

Route::middleware(['auth', 'role:mahasiswa', 'permission:submission.manage'])->group(function () {
    Route::get('/mahasiswa/tugas', [MahasiswaAssignmentController::class, 'index'])->name('mahasiswa.tugas.index');
    Route::post('/mahasiswa/tugas', [MahasiswaAssignmentController::class, 'submit'])->name('mahasiswa.tugas.submit');
});

Route::middleware(['auth', 'role:mahasiswa', 'permission:grades.view'])->group(function () {
    Route::get('/mahasiswa/nilai', [MahasiswaGradeController::class, 'index'])->name('mahasiswa.nilai.index');
    Route::get('/mahasiswa/khs', [KhsController::class, 'index'])->name('mahasiswa.khs.index');
    Route::get('/mahasiswa/transkrip', [TranskripController::class, 'index'])->name('mahasiswa.transkrip.index');
});

Route::middleware(['auth', 'role:mahasiswa', 'permission:schedule.view'])->group(function () {
    Route::get('/mahasiswa/jadwal', [ScheduleController::class, 'index'])->name('mahasiswa.jadwal.index');
    Route::get('/mahasiswa/presensi', [PresenceController::class, 'index'])->name('mahasiswa.presensi.index');
});

Route::middleware(['auth', 'role:mahasiswa', 'permission:thesis.view'])->group(function () {
    Route::get('/mahasiswa/skripsi', [App\Http\Controllers\Mahasiswa\ThesisController::class, 'index'])->name('mahasiswa.skripsi.index');
    Route::post('/mahasiswa/skripsi/log', [App\Http\Controllers\Mahasiswa\ThesisController::class, 'storeLog'])->name('mahasiswa.skripsi.store-log');
});

// Setiap mahasiswa boleh mengelola profilnya sendiri, jadi tidak perlu permission khusus.
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/mahasiswa/profil', [ProfileController::class, 'index'])->name('mahasiswa.profil.index');
    Route::put('/mahasiswa/profil', [ProfileController::class, 'update'])->name('mahasiswa.profil.update');
});

Route::middleware(['auth', 'role:mahasiswa', 'permission:internship.view'])->group(function () {
    Route::get('/mahasiswa/kp', [InternshipController::class, 'index'])->name('mahasiswa.kp.index');
    Route::post('/mahasiswa/kp/log', [InternshipController::class, 'storeLog'])->name('mahasiswa.kp.store-log');
});

Route::middleware(['auth', 'role:pimpinan', 'permission:dashboard.view'])->group(function () {
    Route::get('/pimpinan/dashboard', DashboardController::class)->name('pimpinan.dashboard');
});
