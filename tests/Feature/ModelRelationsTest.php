<?php

use App\Enums\AcademicRank;
use App\Enums\AttendanceStatus;
use App\Enums\CourseType;
use App\Enums\DayOfWeek;
use App\Enums\DegreeLevel;
use App\Enums\GradeLetter;
use App\Enums\InternshipLogApproval;
use App\Enums\InternshipStatus;
use App\Enums\SemesterType;
use App\Enums\StudentStatus;
use App\Enums\StudyPlanDetailStatus;
use App\Enums\StudyPlanStatus;
use App\Enums\ThesisStatus;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\CourseOffering;
use App\Models\Faculty;
use App\Models\Grade;
use App\Models\Internship;
use App\Models\InternshipLog;
use App\Models\Lecturer;
use App\Models\LecturerAttendance;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudyPlan;
use App\Models\StudyPlanDetail;
use App\Models\StudyProgram;
use App\Models\Thesis;
use App\Models\ThesisLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('relasi master, akademik, KRS, dan nilai terhubung dengan benar', function () {
    $faculty = Faculty::create(['code' => 'FT', 'name' => 'Fakultas Teknik']);
    $program = StudyProgram::create([
        'faculty_id' => $faculty->id,
        'code' => 'IF',
        'name' => 'Informatika',
        'degree_level' => DegreeLevel::S1,
    ]);

    expect($faculty->studyPrograms)->toHaveCount(1)
        ->and($program->faculty->is($faculty))->toBeTrue()
        ->and($program->degree_level)->toBeInstanceOf(DegreeLevel::class);

    $year = AcademicYear::create([
        'code' => '2025',
        'name' => '2025/2026',
        'start_date' => '2025-08-01',
        'end_date' => '2026-07-31',
        'is_active' => true,
    ]);
    $semester = Semester::create([
        'academic_year_id' => $year->id,
        'type' => SemesterType::Ganjil,
        'start_date' => '2025-08-01',
        'end_date' => '2025-12-31',
        'is_active' => true,
    ]);

    expect($year->semesters)->toHaveCount(1)
        ->and($semester->academicYear->is($year))->toBeTrue()
        ->and($semester->type)->toBeInstanceOf(SemesterType::class);

    $course = Course::create([
        'study_program_id' => $program->id,
        'code' => 'IF101',
        'name' => 'Pemrograman Dasar',
        'sks' => 3,
        'semester' => 1,
        'type' => CourseType::Wajib,
    ]);

    expect($program->courses)->toHaveCount(1)
        ->and($course->type)->toBeInstanceOf(CourseType::class);

    $studentUser = User::create(['name' => 'Mhs', 'email' => 'mhs@test.com', 'password' => 'password']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'nim' => '2025001',
        'study_program_id' => $program->id,
        'entry_year' => '2025',
        'status' => StudentStatus::Aktif,
    ]);
    $lecturerUser = User::create(['name' => 'Dosen', 'email' => 'dosen@test.com', 'password' => 'password']);
    $lecturer = Lecturer::create([
        'user_id' => $lecturerUser->id,
        'nidn' => '00112233',
        'study_program_id' => $program->id,
        'academic_rank' => AcademicRank::Lektor,
    ]);

    expect($studentUser->student->is($student))->toBeTrue()
        ->and($lecturerUser->lecturer->is($lecturer))->toBeTrue()
        ->and($program->students)->toHaveCount(1)
        ->and($program->lecturers)->toHaveCount(1)
        ->and($student->status)->toBeInstanceOf(StudentStatus::class)
        ->and($lecturer->academic_rank)->toBeInstanceOf(AcademicRank::class);

    $classroom = Classroom::create(['code' => 'R1', 'name' => 'Ruang 1', 'capacity' => 40]);
    $offering = CourseOffering::create([
        'course_id' => $course->id,
        'semester_id' => $semester->id,
        'lecturer_id' => $lecturer->id,
        'classroom_id' => $classroom->id,
        'day' => DayOfWeek::Senin,
        'start_time' => '08:00',
        'end_time' => '10:00',
        'quota' => 40,
    ]);

    expect($offering->course->is($course))->toBeTrue()
        ->and($offering->lecturer->is($lecturer))->toBeTrue()
        ->and($offering->classroom->is($classroom))->toBeTrue()
        ->and($offering->semester->is($semester))->toBeTrue()
        ->and($offering->day)->toBeInstanceOf(DayOfWeek::class);

    $plan = StudyPlan::create([
        'student_id' => $student->id,
        'semester_id' => $semester->id,
        'status' => StudyPlanStatus::Draft,
    ]);
    $detail = StudyPlanDetail::create([
        'study_plan_id' => $plan->id,
        'course_offering_id' => $offering->id,
        'status' => StudyPlanDetailStatus::Pending,
    ]);

    expect($student->studyPlans)->toHaveCount(1)
        ->and($plan->student->is($student))->toBeTrue()
        ->and($plan->studyPlanDetails)->toHaveCount(1)
        ->and($detail->courseOffering->is($offering))->toBeTrue()
        ->and($plan->status)->toBeInstanceOf(StudyPlanStatus::class);

    $grade = Grade::create([
        'study_plan_detail_id' => $detail->id,
        'student_id' => $student->id,
        'course_offering_id' => $offering->id,
        'score' => 87.5,
        'letter_grade' => GradeLetter::A,
        'grade_point' => GradeLetter::A->point(),
    ]);

    expect($detail->grade->is($grade))->toBeTrue()
        ->and($grade->student->is($student))->toBeTrue()
        ->and($grade->letter_grade)->toBeInstanceOf(GradeLetter::class)
        ->and($grade->letter_grade->point())->toBe(4.0);

    $attendance = Attendance::create([
        'course_offering_id' => $offering->id,
        'student_id' => $student->id,
        'meeting_number' => 1,
        'date' => '2025-09-01',
        'status' => AttendanceStatus::Hadir,
    ]);

    expect($offering->attendances)->toHaveCount(1)
        ->and($attendance->student->is($student))->toBeTrue()
        ->and($attendance->status)->toBeInstanceOf(AttendanceStatus::class);
});

test('relasi skripsi, KP, materi, tugas, dan aktivitas terhubung dengan benar', function () {
    $faculty = Faculty::create(['code' => 'FT', 'name' => 'FT']);
    $program = StudyProgram::create([
        'faculty_id' => $faculty->id,
        'code' => 'IF',
        'name' => 'IF',
        'degree_level' => DegreeLevel::S1,
    ]);

    $studentUser = User::create(['name' => 'M', 'email' => 'm@test.com', 'password' => 'password']);
    $student = Student::create([
        'user_id' => $studentUser->id,
        'nim' => '2025002',
        'study_program_id' => $program->id,
        'entry_year' => '2025',
        'status' => StudentStatus::Aktif,
    ]);

    $d1 = Lecturer::create([
        'user_id' => User::create(['name' => 'D1', 'email' => 'd1@test.com', 'password' => 'password'])->id,
        'nidn' => '00000001',
        'academic_rank' => AcademicRank::Lektor,
    ]);
    $d2 = Lecturer::create([
        'user_id' => User::create(['name' => 'D2', 'email' => 'd2@test.com', 'password' => 'password'])->id,
        'nidn' => '00000002',
        'academic_rank' => AcademicRank::LektorKepala,
    ]);

    $thesis = Thesis::create([
        'student_id' => $student->id,
        'title' => 'Judul Skripsi',
        'abstract' => 'Abstrak',
        'supervisor_1_id' => $d1->id,
        'supervisor_2_id' => $d2->id,
        'status' => ThesisStatus::Proposal,
        'submission_date' => '2025-09-01',
    ]);
    $thesisLog = ThesisLog::create([
        'thesis_id' => $thesis->id,
        'date' => '2025-09-02',
        'activity' => 'Bimbingan',
        'notes' => 'Revisi bab 1',
        'supervisor_approval' => true,
    ]);

    expect($student->thesis->is($thesis))->toBeTrue()
        ->and($thesis->supervisorOne->is($d1))->toBeTrue()
        ->and($thesis->supervisorTwo->is($d2))->toBeTrue()
        ->and($thesis->thesisLogs)->toHaveCount(1)
        ->and($thesisLog->thesis->is($thesis))->toBeTrue()
        ->and($d1->supervisedTheses)->toHaveCount(1)
        ->and($d2->coSupervisedTheses)->toHaveCount(1);

    $internship = Internship::create([
        'student_id' => $student->id,
        'company_name' => 'PT X',
        'address' => 'Alamat',
        'supervisor_id' => $d1->id,
        'field_supervisor' => 'Bpk Y',
        'start_date' => '2025-09-01',
        'end_date' => '2025-10-01',
        'status' => InternshipStatus::Berjalan,
    ]);
    $internshipLog = InternshipLog::create([
        'internship_id' => $internship->id,
        'date' => '2025-09-02',
        'activity' => 'Kegiatan',
        'notes' => 'Catatan',
        'approval' => InternshipLogApproval::Pending,
    ]);

    expect($student->internship->is($internship))->toBeTrue()
        ->and($internship->supervisor->is($d1))->toBeTrue()
        ->and($internship->internshipLogs)->toHaveCount(1)
        ->and($internshipLog->internship->is($internship))->toBeTrue()
        ->and($internshipLog->approval)->toBeInstanceOf(InternshipLogApproval::class)
        ->and($d1->supervisedInternships)->toHaveCount(1);

    $year = AcademicYear::create([
        'code' => '2025', 'name' => '2025/2026',
        'start_date' => '2025-08-01', 'end_date' => '2026-07-31', 'is_active' => true,
    ]);
    $semester = Semester::create([
        'academic_year_id' => $year->id,
        'type' => SemesterType::Ganjil,
        'start_date' => '2025-08-01', 'end_date' => '2025-12-31', 'is_active' => true,
    ]);
    $course = Course::create([
        'study_program_id' => $program->id,
        'code' => 'IF102', 'name' => 'Algoritma', 'sks' => 3, 'semester' => 1, 'type' => CourseType::Wajib,
    ]);
    $classroom = Classroom::create(['code' => 'R2', 'name' => 'Ruang 2', 'capacity' => 40]);
    $offering = CourseOffering::create([
        'course_id' => $course->id,
        'semester_id' => $semester->id,
        'lecturer_id' => $d1->id,
        'classroom_id' => $classroom->id,
        'day' => DayOfWeek::Selasa,
        'start_time' => '08:00', 'end_time' => '10:00', 'quota' => 40,
    ]);

    $material = CourseMaterial::create([
        'course_offering_id' => $offering->id,
        'title' => 'Materi 1',
        'description' => 'Deskripsi',
        'uploaded_by' => $d1->user_id,
    ]);
    $assignment = Assignment::create([
        'course_offering_id' => $offering->id,
        'title' => 'Tugas 1',
        'description' => 'Deskripsi tugas',
        'due_date' => '2025-09-10 23:59:59',
        'max_score' => 100.0,
    ]);
    $submission = AssignmentSubmission::create([
        'assignment_id' => $assignment->id,
        'student_id' => $student->id,
        'file_path' => '/uploads/tugas1.pdf',
        'score' => 90.0,
        'feedback' => 'Bagus',
    ]);
    $lecturerAttendance = LecturerAttendance::create([
        'lecturer_id' => $d1->id,
        'course_offering_id' => $offering->id,
        'date' => '2025-09-01',
        'check_in' => '07:55',
        'check_out' => '10:05',
        'status' => \App\Enums\LecturerAttendanceStatus::Hadir,
    ]);

    expect($offering->courseMaterials)->toHaveCount(1)
        ->and($offering->assignments)->toHaveCount(1)
        ->and($assignment->assignmentSubmissions)->toHaveCount(1)
        ->and($submission->student->is($student))->toBeTrue()
        ->and($offering->lecturerAttendances)->toHaveCount(1)
        ->and($material->uploadedBy->is($d1->user))->toBeTrue();

    $announcement = Announcement::create([
        'title' => 'Pengumuman',
        'content' => 'Isi',
        'target_role' => 'student',
        'published_at' => '2025-09-01 08:00:00',
    ]);
    $activityLog = ActivityLog::create([
        'user_id' => $studentUser->id,
        'action' => 'create',
        'model_type' => StudyPlan::class,
        'model_id' => 1,
        'old_values' => ['status' => 'draft'],
        'new_values' => ['status' => 'submitted'],
    ]);

    expect(Announcement::count())->toBe(1)
        ->and($activityLog->user->is($studentUser))->toBeTrue();
});
