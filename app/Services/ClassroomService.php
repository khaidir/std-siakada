<?php

namespace App\Services;

use App\DTO\ClassroomData;
use App\Models\Classroom;
use App\Repositories\Contracts\ClassroomRepository;
use Illuminate\Validation\ValidationException;

class ClassroomService
{
    public function __construct(
        private readonly ClassroomRepository $classrooms,
    ) {}

    /**
     * Data halaman daftar ruangan.
     *
     * @return array<string, mixed>
     */
    public function pageData(): array
    {
        $items = $this->classrooms->listAll()
            ->map(fn (Classroom $c) => [
                'id' => $c->id,
                'code' => $c->code,
                'name' => $c->name,
                'capacity' => $c->capacity,
                'building' => $c->building,
            ])
            ->values()
            ->all();

        return ['classrooms' => $items];
    }

    public function create(ClassroomData $data): void
    {
        $existing = $this->classrooms->findByCode($data->code);

        if ($existing) {
            throw ValidationException::withMessages([
                'code' => 'Kode ruangan sudah digunakan.',
            ]);
        }

        $this->classrooms->create([
            'code' => $data->code,
            'name' => $data->name,
            'capacity' => $data->capacity,
            'building' => $data->building,
        ]);
    }

    public function update(int $id, ClassroomData $data): void
    {
        $existing = $this->classrooms->findByCode($data->code);

        if ($existing && $existing->id !== $id) {
            throw ValidationException::withMessages([
                'code' => 'Kode ruangan sudah digunakan.',
            ]);
        }

        $this->classrooms->update($id, [
            'code' => $data->code,
            'name' => $data->name,
            'capacity' => $data->capacity,
            'building' => $data->building,
        ]);
    }

    public function delete(int $id): void
    {
        $this->classrooms->delete($id);
    }
}
