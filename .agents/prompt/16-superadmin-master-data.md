# 16 — Prompt: Super Admin — Master Data (Prodi, Kelas/Jadwal, Pengumuman)

```text
Buatkan fitur master data untuk super-admin.

A. Fakultas (pages/admin/Faculties.vue):
   - CRUD faculties (kode, nama).
   - DTO FacultyData; FacultyRepository; FacultyService; FacultyController + Request; Policy (super-admin).

B. Program Studi (pages/admin/StudyPrograms.vue):
   - CRUD study_programs (fakultas, kode, nama, jenjang/degree_level).
   - DTO StudyProgramData; StudyProgramRepository (list: SELECT id, faculty_id, code, name, degree_level + eager-load faculty.id,name); StudyProgramService; StudyProgramController + Request; Policy.

C. Mata Kuliah (pages/admin/Courses.vue):
   - CRUD courses (prodi, kode, nama, sks, semester, type).
   - DTO CourseData; CourseRepository; CourseService; CourseController + Request; Policy.

D. Ruangan (pages/admin/Classrooms.vue):
   - CRUD classrooms (kode, nama, kapasitas, gedung).
   - DTO ClassroomData; ClassroomRepository; ClassroomService; ClassroomController + Request; Policy.

E. Kelas & Jadwal (pages/admin/CourseOfferings.vue):
   - CRUD course_offerings: pilih mata kuliah, dosen pengampu, semester (dari periode akademik), ruangan, hari, jam, kuota.
   - DTO CourseOfferingData; CourseOfferingRepository (list: SELECT id, course_id, lecturer_id, semester_id, classroom_id, day, start_time, end_time, quota + eager-load course.id,code,name & lecturer.user.id,name & classroom.id,name); CourseOfferingService; Controller + Request; Policy.

F. Periode Akademik (pages/admin/AcademicPeriods.vue):
   - CRUD academic_years (kode, nama, start_date, end_date, is_active) + semesters (academic_year_id, type, start_date, end_date, is_active).
   - DTO AcademicYearData, SemesterData; AcademicPeriodRepository; AcademicPeriodService; AcademicPeriodController + Request; Policy.

G. Pengumuman (pages/admin/Announcements.vue):
   - CRUD announcements (judul, isi, target_role opsional, published_at).
   - DTO AnnouncementData; AnnouncementRepository; AnnouncementService; Controller + Request; Policy.

ATURAN:
- Hanya super-admin yang akses master data.
- Validasi enum (degree_level, semester_type, day) via PHP enum.
- Hanya satu semester aktif pada satu waktu.
- Tidak ada SELECT *.

PEST TEST:
- FacultyTest, StudyProgramTest, CourseTest, ClassroomTest, CourseOfferingTest, AcademicPeriodTest, AnnouncementTest: CRUD super-admin sukses; role lain 403.
```
