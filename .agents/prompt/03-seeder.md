# 03 — Prompt: Seeder (Lengkap per PRD)

```text
Buatkan seeder lengkap untuk SIAKAD.

1. RoleAndPermissionSeeder:
   - Roles: super-admin, kaprodi, dosen, mahasiswa, pimpinan.
   - Permissions (sesuai plan/04):
     dashboard.view, master.view, master.create, master.update, master.delete,
     users.view, users.create, users.update, users.delete, roles.manage,
     courses.view, courses.create, courses.update, courses.delete,
     offerings.view, offering.manage, period.manage,
     krs.manage, krs.approve,
     grades.view, grades.manage,
     attendance.view, attendance.manage,
     material.view, material.manage,
     assignment.manage, submission.manage,
     thesis.view, thesis.manage, internship.view, internship.manage,
     lecturer-attendance.view, lecturer-attendance.manage,
     advisor.manage, ai-advisor.use, report.view,
     announcement.view, announcement.manage.
   - Mapping:
     - super-admin: (Gate::before true)
     - kaprodi: dashboard.view, courses.view/create/update/delete, offerings.view, grades.view, thesis.view, internship.view, lecturer-attendance.view, announcement.view
     - dosen: dashboard.view, offerings.view, grades.view/ manage, attendance.view/manage, material.view/manage, assignment.manage, krs.approve, thesis.view, internship.view, lecturer-attendance.view, announcement.view
     - mahasiswa: dashboard.view, offerings.view, krs.manage, material.view, submission.manage, grades.view, thesis.view, internship.view, ai-advisor.use, announcement.view
     - pimpinan: dashboard.view, report.view, grades.view, attendance.view, thesis.view, internship.view, lecturer-attendance.view, announcement.view

2. MasterDataSeeder:
   - 2 faculties (mis. Fakultas Teknik, Fakultas Ilmu Komputer).
   - 3 study_programs per fakultas (Informatika, Sistem Informasi, Teknik Komputer, dst.).
   - 3+ courses per prodi (kode, nama, sks, semester, type wajib/pilihan).
   - 2 classrooms (ruangan) + 1 academic_year aktif + 2 semesters (ganjil/genap).
   - 2 course_offerings per course (semester aktif, jadwal hari/jam, dosen pengampu, kuota).

3. UserSeeder (akun default untuk login/testing):
   - super-admin: admin@siakad.test / password
   - kaprodi: kaprodi@siakad.test / password
   - dosen: dosen@siakad.test / password
   - mahasiswa: mahasiswa@siakad.test / password
   - pimpinan: pimpinan@siakad.test / password
   (Semua password pakai Hash; buat profil students/lecturers yang sesuai; assign role; beri mahasiswa study_plan + study_plan_details (approved) + grade + attendance + thesis/internship agar dashboard/KHS/nilai/skripsi/KP tampil.)

4. Factory:
   - UserFactory, FacultyFactory, StudyProgramFactory, CourseFactory, ClassroomFactory, AcademicYearFactory, SemesterFactory, StudentFactory, LecturerFactory, CourseOfferingFactory, StudyPlanFactory, StudyPlanDetailFactory, GradeFactory, AttendanceFactory, LecturerAttendanceFactory, MaterialFactory, AssignmentFactory, SubmissionFactory, ThesisFactory, ThesisLogFactory, InternshipFactory, InternshipLogFactory, AnnouncementFactory.

PASTIKAN seeder idempoten (gunakan firstOrCreate) dan semua query memakai ->select([...]) bila membaca data (bukan SELECT *).
```
