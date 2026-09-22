<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

/**
 * Field biodata yang boleh diubah mahasiswa sendiri.
 *
 * DTO ini sengaja TIDAK memuat nim, gpa, total_sks, status, study_program_id,
 * maupun entry_year. Data akademik hanya boleh diubah admin, sehingga field
 * tersebut tidak pernah ikut terbawa walau dikirim di payload.
 */
class StudentProfileData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $birth_place = null,
        public ?string $birth_date = null,
        public ?string $gender = null,
        public ?string $address = null,
        public ?string $phone = null,
    ) {}
}
