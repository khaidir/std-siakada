<?php

namespace App\Services;

use App\DTO\FacultyData;
use App\Models\Faculty;
use App\Repositories\Contracts\FacultyRepository;
use Illuminate\Validation\ValidationException;

class FacultyService
{
    public function __construct(
        private readonly FacultyRepository $faculties,
    ) {}

    /**
     * Data halaman daftar fakultas.
     *
     * @return array<string, mixed>
     */
    public function pageData(): array
    {
        $items = $this->faculties->listAll()
            ->map(fn (Faculty $f) => [
                'id' => $f->id,
                'code' => $f->code,
                'name' => $f->name,
            ])
            ->values()
            ->all();

        return ['faculties' => $items];
    }

    public function create(FacultyData $data): void
    {
        $existing = $this->faculties->findByCode($data->code);

        if ($existing) {
            throw ValidationException::withMessages([
                'code' => 'Kode fakultas sudah digunakan.',
            ]);
        }

        $this->faculties->create([
            'code' => $data->code,
            'name' => $data->name,
        ]);
    }

    public function update(int $id, FacultyData $data): void
    {
        $existing = $this->faculties->findByCode($data->code);

        if ($existing && $existing->id !== $id) {
            throw ValidationException::withMessages([
                'code' => 'Kode fakultas sudah digunakan.',
            ]);
        }

        $this->faculties->update($id, [
            'code' => $data->code,
            'name' => $data->name,
        ]);
    }

    public function delete(int $id): void
    {
        $this->faculties->delete($id);
    }
}
