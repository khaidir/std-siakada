# 02 — Prompt: Migrasi Database, Model & Enum (Lengkap per PRD)

```text
Buatkan migrasi, model Eloquent, dan PHP enums untuk SIAKAD (lengkap per PRD).

ATURAN:
- PK $table->id(); FK $table->foreignId('x_id')->constrained()->cascadeOnDelete() (relasi opsional ->nullOnDelete()).
- Index pada kolom sering di-query (nim, nidn, code, semester_id, student_id, course_offering_id, assignment_id).
- Status/tipe pakai $table->string(...) (bukan enum DB); nilai divalidasi via PHP enum.
- Semua tabel $table->timestamps().
- Tabel master (faculties, study_programs, courses, classrooms) pakai $table->softDeletes().

TABEL (urutan migrasi agar FK benar):
1. users: id, name, email(unique), password, email_verified_at(nullable), remember_token, timestamps.
2. faculties: id, code(unique), name, timestamps, softDeletes.
3. study_programs: id, faculty_id(fk), code(unique), name, degree_level(string), head_id(fk users nullable), timestamps, softDeletes.
4. academic_years: id, code(unique), name, start_date(date), end_date(date), is_active(boolean default false), timestamps.
5. semesters: id, academic_year_id(fk), type(string), start_date(date), end_date(date), is_active(boolean default false), timestamps.
6. courses: id, study_program_id(fk), code(unique), name, sks(tinyInteger), semester(integer), type(string), timestamps, softDeletes.
7. classrooms: id, code(unique), name, capacity(integer), building(string nullable), timestamps, softDeletes.
8. students: id, user_id(fk users unique), nim(unique), study_program_id(fk), entry_year(string), status(string), gpa(decimal 5,2 default 0), total_sks(integer default 0), timestamps.
9. lecturers: id, user_id(fk users unique), nidn(unique), study_program_id(fk nullable), academic_rank(string), timestamps.
10. course_offerings: id, course_id(fk), semester_id(fk), lecturer_id(fk lecturers), classroom_id(fk), day(string), start_time(time), end_time(time), quota(integer), timestamps.
11. study_plans: id, student_id(fk students), semester_id(fk), status(string default 'draft'), approved_by(fk users nullable), approved_at(timestamp nullable), timestamps.
12. study_plan_details: id, study_plan_id(fk), course_offering_id(fk), status(string default 'pending'), timestamps.
13. grades: id, study_plan_detail_id(fk unique), student_id(fk students), course_offering_id(fk), assignment_score(decimal 5,2 nullable), midterm_score(decimal 5,2 nullable), final_score(decimal 5,2 nullable), score(decimal 5,2), letter_grade(string 2), grade_point(decimal 4,2), timestamps.
14. attendances: id, course_offering_id(fk), student_id(fk students), meeting_number(integer), date(date), status(string), timestamps.
15. lecturer_attendances: id, lecturer_id(fk lecturers), course_offering_id(fk), date(date), check_in(time nullable), check_out(time nullable), status(string), timestamps.
16. theses: id, student_id(fk students unique), title, abstract(text), supervisor_1_id(fk lecturers), supervisor_2_id(fk lecturers nullable), status(string), submission_date(date), timestamps.
17. thesis_logs: id, thesis_id(fk), date(date), activity, notes(text), supervisor_approval(boolean default false), timestamps.
18. internships: id, student_id(fk students), company_name, address(text), supervisor_id(fk lecturers), field_supervisor(string), start_date(date), end_date(date), status(string), timestamps.
19. internship_logs: id, internship_id(fk), date(date), activity, notes(text), approval(string default 'pending'), timestamps.
20. course_materials: id, course_offering_id(fk), title, description(text nullable), file_path(string nullable), uploaded_by(fk users), timestamps.
21. assignments: id, course_offering_id(fk), title, description(text), due_date(datetime), max_score(decimal), timestamps.
22. assignment_submissions: id, assignment_id(fk), student_id(fk students), file_path, submitted_at(timestamp nullable), score(decimal nullable), feedback(text nullable), timestamps.
23. announcements: id, title, content(text), target_role(string nullable), published_at(timestamp nullable), timestamps.
24. activity_logs: id, user_id(fk users nullable), action(string), model_type(string), model_id(bigInteger nullable), old_values(json nullable), new_values(json nullable), timestamps.

ENUM (PHP backed enum, app/Enums/):
- DegreeLevel: d3, d4, s1, s2, s3
- SemesterType: ganjil, genap
- CourseType: wajib, pilihan
- DayOfWeek: senin, selasa, rabu, kamis, jumat, sabtu, minggu
- StudentStatus: aktif, cuti, lulus, do, nonaktif
- AcademicRank: asisten_ahli, lektor, lektor_kepala, guru_besar
- StudyPlanStatus: draft, submitted, approved, rejected
- StudyPlanDetailStatus: pending, approved, rejected
- AttendanceStatus: hadir, izin, sakit, alpha
- LecturerAttendanceStatus: hadir, terlambat, izin, alpha
- ThesisStatus: proposal, seminar_proposal, sidang, lulus, revisi
- InternshipStatus: draft, berjalan, selesai, ditolak
- InternshipLogApproval: pending, approved, rejected
- GradeLetter: A=4.0, A_MINUS=3.75, B_PLUS=3.25, B=3.0, B_MINUS=2.75, C_PLUS=2.25, C=2.0, D=1.0, E=0 (dengan label huruf)

MODEL ELOQUENT (untuk SEMUA tabel):
- Relasi lengkap (hasMany, belongsTo, hasOne) dengan return type tepat.
- $fillable atau $guarded; $casts (enum, datetime, decimal, json).
- Relasi kunci:
  - Faculty hasMany StudyProgram; StudyProgram belongsTo Faculty, hasMany Student/Lecturer/Course.
  - AcademicYear hasMany Semester; Semester belongsTo AcademicYear, hasMany CourseOffering/StudyPlan.
  - User hasOne Student/Lecturer; Student belongsTo User/StudyProgram; Lecturer belongsTo User/StudyProgram.
  - Course belongsTo StudyProgram, hasMany CourseOffering.
  - CourseOffering belongsTo Course/Lecturer/Semester/Classroom, hasMany StudyPlanDetail/Attendance/LecturerAttendance/CourseMaterial/Assignment.
  - StudyPlan belongsTo Student/Semester/User(approvedBy), hasMany StudyPlanDetail.
  - StudyPlanDetail belongsTo StudyPlan/CourseOffering, hasOne Grade.
  - Grade belongsTo StudyPlanDetail/Student/CourseOffering.
  - Attendance belongsTo CourseOffering/Student; LecturerAttendance belongsTo Lecturer/CourseOffering.
  - Thesis belongsTo Student + supervisor_1/supervisor_2 (Lecturer), hasMany ThesisLog.
  - Internship belongsTo Student/Lecturer(supervisor), hasMany InternshipLog.
  - Assignment hasMany AssignmentSubmission; AssignmentSubmission belongsTo Assignment/Student.

PASTIKAN: tidak ada SELECT * di seluruh kode — semua query memakai ->select([...]) dengan kolom yang dibutuhkan.
```
