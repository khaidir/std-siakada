<?php

namespace App\Services;

use App\DTO\CourseOfferingData;
use App\Models\CourseOffering;
use App\Repositories\Contracts\CourseOfferingRepository;
use Illuminate\Validation\ValidationException;

class CourseOfferingService
{
    public function __construct(
        private readonly CourseOfferingRepository $offerings,
    ) {}

    /**
     * Data halaman daftar kelas & jadwal.
     *
     * @return array<string, mixed>
     */
    public function pageData(): array
    {
        $items = $this->offerings->listAll()
            ->map(fn (CourseOffering $o) => [
                'id' => $o->id,
                'course_id' => $o->course_id,
                'course_code' => $o->course?->code ?? '',
                'course_name' => $o->course?->name ?? '',
                'semester_id' => $o->semester_id,
                'semester_label' => $o->semester?->type?->value . ' ' . $o->semester?->academicYear?->code ?? '',
                'lecturer_id' => $o->lecturer_id,
                'lecturer_name' => $o->lecturer?->user?->name ?? '',
                'classroom_id' => $o->classroom_id,
                'classroom_name' => $o->classroom?->name ?? '',
                'day' => $o->day?->value ?? $o->day,
                'start_time' => $o->start_time,
                'end_time' => $o->end_time,
                'quota' => $o->quota,
            ])
            ->values()
            ->all();

        $courses = \App\Models\Course::query()
            ->select(['id', 'code', 'name'])
            ->orderBy('code')
            ->get()
            ->map(fn ($c) => ['id' => $c->id, 'code' => $c->code, 'name' => $c->name])
            ->values()
            ->all();

        $semesters = \App\Models\Semester::query()
            ->select(['id', 'academic_year_id', 'type'])
            ->with('academicYear:id,code')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(fn ($s) => ['id' => $s->id, 'label' => ($s->type?->value ?? $s->type) . ' ' . ($s->academicYear?->code ?? '')])
            ->values()
            ->all();

        $lecturers = \App\Models\Lecturer::query()
            ->select(['id', 'user_id', 'study_program_id'])
            ->with('user:id,name')
            ->get()
            ->map(fn ($l) => ['id' => $l->id, 'name' => $l->user?->name ?? ''])
            ->values()
            ->all();

        $classrooms = \App\Models\Classroom::query()
            ->select(['id', 'code', 'name'])
            ->orderBy('code')
            ->get()
            ->map(fn ($r) => ['id' => $r->id, 'code' => $r->code, 'name' => $r->name])
            ->values()
            ->all();

        return [
            'offerings' => $items,
            'courses' => $courses,
            'semesters' => $semesters,
            'lecturers' => $lecturers,
            'classrooms' => $classrooms,
        ];
    }

    public function create(CourseOfferingData $data): void
    {
        $this->offerings->create([
            'course_id' => $data->course_id,
            'semester_id' => $data->semester_id,
            'lecturer_id' => $data->lecturer_id,
            'classroom_id' => $data->classroom_id,
            'day' => $data->day,
            'start_time' => $data->start_time,
            'end_time' => $data->end_time,
            'quota' => $data->quota,
        ]);
    }

    public function update(int $id, CourseOfferingData $data): void
    {
        $this->offerings->update($id, [
            'course_id' => $data->course_id,
            'semester_id' => $data->semester_id,
            'lecturer_id' => $data->lecturer_id,
            'classroom_id' => $data->classroom_id,
            'day' => $data->day,
            'start_time' => $data->start_time,
            'end_time' => $data->end_time,
            'quota' => $data->quota,
        ]);
    }

    public function delete(int $id): void
    {
        $this->offerings->delete($id);
    }
}
