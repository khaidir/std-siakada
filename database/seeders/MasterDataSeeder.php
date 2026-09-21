<?php

namespace Database\Seeders;

use App\Enums\CourseType;
use App\Enums\DayOfWeek;
use App\Enums\DegreeLevel;
use App\Enums\SemesterType;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Faculty;
use App\Models\Lecturer;
use App\Models\Semester;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $faculties = $this->seedFaculties();
        $programs = $this->seedStudyPrograms($faculties);
        $courses = $this->seedCourses($programs);
        $classrooms = $this->seedClassrooms();
        $activeSemester = $this->seedAcademicPeriods();
        $lecturers = $this->seedLecturers($programs);

        $this->seedCourseOfferings($courses, $classrooms, $activeSemester, $lecturers);
    }

    /**
     * @return array<int, Faculty>
     */
    private function seedFaculties(): array
    {
        $data = [
            ['code' => 'FT', 'name' => 'Fakultas Teknik'],
            ['code' => 'FIK', 'name' => 'Fakultas Ilmu Komputer'],
        ];

        $faculties = [];
        foreach ($data as $row) {
            $faculties[] = Faculty::firstOrCreate(
                ['code' => $row['code']],
                ['name' => $row['name']],
            );
        }

        return $faculties;
    }

    /**
     * @param  array<int, Faculty>  $faculties
     * @return array<int, StudyProgram>
     */
    private function seedStudyPrograms(array $faculties): array
    {
        [$ft, $fik] = $faculties;

        $data = [
            ['faculty' => $ft, 'code' => 'IF', 'name' => 'Informatika'],
            ['faculty' => $ft, 'code' => 'TK', 'name' => 'Teknik Komputer'],
            ['faculty' => $ft, 'code' => 'TE', 'name' => 'Teknik Elektro'],
            ['faculty' => $fik, 'code' => 'SI', 'name' => 'Sistem Informasi'],
            ['faculty' => $fik, 'code' => 'IK', 'name' => 'Ilmu Komputer'],
            ['faculty' => $fik, 'code' => 'TI', 'name' => 'Teknologi Informasi'],
        ];

        $programs = [];
        foreach ($data as $row) {
            $programs[] = StudyProgram::firstOrCreate(
                ['code' => $row['code']],
                [
                    'faculty_id' => $row['faculty']->id,
                    'name' => $row['name'],
                    'degree_level' => DegreeLevel::S1,
                ],
            );
        }

        return $programs;
    }

    /**
     * @param  array<int, StudyProgram>  $programs
     * @return array<int, Course>
     */
    private function seedCourses(array $programs): array
    {
        $map = [
            'IF' => [
                ['IF101', 'Pemrograman Dasar', 1, CourseType::Wajib],
                ['IF102', 'Algoritma & Struktur Data', 1, CourseType::Wajib],
                ['IF201', 'Basis Data', 2, CourseType::Wajib],
                ['IF301', 'Kecerdasan Buatan', 3, CourseType::Pilihan],
            ],
            'TK' => [
                ['TK101', 'Elektronika Dasar', 1, CourseType::Wajib],
                ['TK201', 'Mikroprosesor', 2, CourseType::Wajib],
                ['TK301', 'Sistem Embedded', 3, CourseType::Pilihan],
            ],
            'TE' => [
                ['TE101', 'Rangkaian Listrik', 1, CourseType::Wajib],
                ['TE201', 'Elektronika', 2, CourseType::Wajib],
                ['TE301', 'Sistem Kendali', 3, CourseType::Pilihan],
            ],
            'SI' => [
                ['SI101', 'Pengantar Sistem Informasi', 1, CourseType::Wajib],
                ['SI201', 'Analisis & Perancangan SI', 2, CourseType::Wajib],
                ['SI301', 'Manajemen Proyek TI', 3, CourseType::Pilihan],
            ],
            'IK' => [
                ['IK101', 'Matematika Diskrit', 1, CourseType::Wajib],
                ['IK201', 'Sistem Operasi', 2, CourseType::Wajib],
                ['IK301', 'Jaringan Komputer', 3, CourseType::Pilihan],
            ],
            'TI' => [
                ['TI101', 'Pengantar Teknologi Informasi', 1, CourseType::Wajib],
                ['TI201', 'Manajemen Data', 2, CourseType::Wajib],
                ['TI301', 'Keamanan Informasi', 3, CourseType::Pilihan],
            ],
        ];

        $courses = [];
        foreach ($programs as $program) {
            foreach ($map[$program->code] as [$code, $name, $semester, $type]) {
                $courses[] = Course::firstOrCreate(
                    ['code' => $code],
                    [
                        'study_program_id' => $program->id,
                        'name' => $name,
                        'sks' => 3,
                        'semester' => $semester,
                        'type' => $type,
                    ],
                );
            }
        }

        return $courses;
    }

    /**
     * @return array<int, Classroom>
     */
    private function seedClassrooms(): array
    {
        $data = [
            ['code' => 'R1', 'name' => 'Ruang 1', 'capacity' => 40, 'building' => 'A'],
            ['code' => 'R2', 'name' => 'Ruang 2', 'capacity' => 30, 'building' => 'A'],
        ];

        $classrooms = [];
        foreach ($data as $row) {
            $classrooms[] = Classroom::firstOrCreate(
                ['code' => $row['code']],
                [
                    'name' => $row['name'],
                    'capacity' => $row['capacity'],
                    'building' => $row['building'],
                ],
            );
        }

        return $classrooms;
    }

    private function seedAcademicPeriods(): Semester
    {
        $year = AcademicYear::firstOrCreate(
            ['code' => '2025'],
            [
                'name' => '2025/2026',
                'start_date' => '2025-08-01',
                'end_date' => '2026-07-31',
                'is_active' => true,
            ],
        );

        Semester::firstOrCreate(
            ['academic_year_id' => $year->id, 'type' => SemesterType::Ganjil->value],
            [
                'start_date' => '2025-08-01',
                'end_date' => '2025-12-31',
                'is_active' => true,
            ],
        );

        Semester::firstOrCreate(
            ['academic_year_id' => $year->id, 'type' => SemesterType::Genap->value],
            [
                'start_date' => '2026-01-01',
                'end_date' => '2026-07-31',
                'is_active' => false,
            ],
        );

        return Semester::query()
            ->where('academic_year_id', $year->id)
            ->where('type', SemesterType::Ganjil->value)
            ->select(['id', 'academic_year_id', 'type'])
            ->firstOrFail();
    }

    /**
     * @param  array<int, StudyProgram>  $programs
     * @return array<int, Lecturer>
     */
    private function seedLecturers(array $programs): array
    {
        $lecturers = [];
        foreach (range(0, 3) as $i) {
            $lecturers[] = Lecturer::factory()->create([
                'study_program_id' => $programs[$i % count($programs)]->id,
            ]);
        }

        return $lecturers;
    }

    /**
     * @param  array<int, Course>  $courses
     * @param  array<int, Classroom>  $classrooms
     * @param  array<int, Lecturer>  $lecturers
     */
    private function seedCourseOfferings(
        array $courses,
        array $classrooms,
        Semester $semester,
        array $lecturers,
    ): void {
        $days = [
            DayOfWeek::Senin,
            DayOfWeek::Selasa,
            DayOfWeek::Rabu,
            DayOfWeek::Kamis,
            DayOfWeek::Jumat,
        ];
        $slots = [
            ['08:00:00', '10:00:00'],
            ['10:10:00', '12:10:00'],
        ];

        $i = 0;
        foreach ($courses as $course) {
            foreach ($slots as $slot) {
                $lecturer = $lecturers[$i % count($lecturers)];
                $classroom = $classrooms[$i % count($classrooms)];
                $day = $days[$i % count($days)];

                CourseOffering::firstOrCreate(
                    [
                        'course_id' => $course->id,
                        'semester_id' => $semester->id,
                        'day' => $day->value,
                        'start_time' => $slot[0],
                    ],
                    [
                        'lecturer_id' => $lecturer->id,
                        'classroom_id' => $classroom->id,
                        'end_time' => $slot[1],
                        'quota' => $classroom->capacity,
                    ],
                );

                $i++;
            }
        }
    }
}
