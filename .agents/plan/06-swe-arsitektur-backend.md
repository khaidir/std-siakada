# 06 — Senior SWE: Arsitektur Backend & Konvensi Kode

## 1. Struktur Folder `app/`

```
app/
├── Enums/                       # PHP backed enums
├── Models/
├── DTO/                         # spatie/laravel-data
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/                # FormRequest (validasi + authorize)
├── Services/
├── Repositories/
│   ├── Contracts/               # interface
│   └── Eloquent/                # implementasi
├── Policies/
└── Support/                     # GradeCalculator, dll.
```

## 2. Kontrak Layer (contoh nyata)

### Repository interface

```php
// app/Repositories/Contracts/GradeRepository.php
interface GradeRepository
{
    public function listByOffering(int $offeringId): Collection;
    public function upsert(GradeData $data): void;
    public function findByDetail(int $studyPlanDetailId): ?Grade;
}
```

### Implementasi — TANPA `SELECT *`

```php
// app/Repositories/Eloquent/GradeRepository.php
class GradeRepository implements GradeRepository
{
    public function listByOffering(int $offeringId): Collection
    {
        return Grade::query()
            ->select(['id', 'study_plan_detail_id', 'student_id', 'score', 'letter_grade', 'grade_point'])
            ->with(['studyPlanDetail:id,study_plan_id,course_offering_id',
                    'student:id,user_id,nim',
                    'student.user:id,name'])
            ->whereHas('studyPlanDetail', fn ($q) =>
                $q->where('course_offering_id', $offeringId))
            ->get();
    }
}
```

### Service

```php
// app/Services/GradeService.php
class GradeService
{
    public function __construct(
        private readonly GradeRepository $grades,
        private readonly StudyPlanDetailRepository $details,
    ) {}

    public function store(GradeData $data): void
    {
        DB::transaction(function () use ($data) {
            [$letter, $point] = GradeCalculator::letterAndPoint($data->score);
            $this->grades->upsert($data->with(letter_grade: $letter, grade_point: $point));
            $this->grades->recalculateGpa($data->student_id);
        });
    }
}
```

### Controller (tipis)

```php
// app/Http/Controllers/Dosen/GradeController.php
class GradeController extends Controller
{
    public function store(StoreGradeRequest $request, GradeService $service)
    {
        $this->authorize('update', Grade::class); // Policy
        $service->store($request->toGradeData());
        return back()->with('success', 'Nilai tersimpan.');
    }
}
```

## 3. Konvensi

- **Penamaan**: singular model, plural tabel, controller di namespace per role (`App\Http\Controllers\Dosen`, `\Mahasiswa`, `\Kaprodi`, `\Admin`).
- **Route name**: `<role>.<fitur>.<aksi>` → `dosen.nilai.store`, `mahasiswa.krs.store`.
- **N+1**: selalu eager-load kolom spesifik; tidak lazy-load di loop.
- **FormRequest**: validasi + `authorize()` (role check) + method `to<Xxx>Data()`.
- **Pint** sebagai formatter baku.

## 4. Daftar Service & Repository (lengkap per PRD)

| Domain | Service | Repository |
|---|---|---|
| Auth | — (Fortify) | — |
| User/Role | `UserService`, `RoleService` | `UserRepository` |
| Fakultas | `FacultyService` | `FacultyRepository` |
| Prodi | `StudyProgramService` | `StudyProgramRepository` |
| Mata kuliah | `CourseService` | `CourseRepository` |
| Ruangan | `ClassroomService` | `ClassroomRepository` |
| Periode akademik | `AcademicPeriodService` | `AcademicPeriodRepository` |
| Kelas/jadwal | `CourseOfferingService` | `CourseOfferingRepository` |
| KRS | `KrsService`, `KrsApprovalService`, `KrsMonitoringService` | `StudyPlanRepository`, `StudyPlanDetailRepository` |
| KHS/Transkrip | `KhsService` | `GradeRepository` |
| Nilai | `GradeService` | `GradeRepository` |
| Absen | `AttendanceService` | `AttendanceRepository` |
| Kehadiran dosen | `LecturerAttendanceService` | `LecturerAttendanceRepository` |
| Materi | `MaterialService` | `MaterialRepository` |
| Tugas | `AssignmentService` | `AssignmentRepository`, `SubmissionRepository` |
| Skripsi | `ThesisService` | `ThesisRepository` |
| KP | `InternshipService` | `InternshipRepository` |
| AI Advisor | `AiAdvisorService` | `ChatRepository` |
| Laporan | `ReportService` | `ReportRepository` |
| Pengumuman | `AnnouncementService` | `AnnouncementRepository` |
