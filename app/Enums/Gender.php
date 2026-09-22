<?php

namespace App\Enums;

/**
 * Jenis kelamin mahasiswa.
 *
 * Backing value adalah string yang disimpan di kolom students.gender.
 */
enum Gender: string
{
    case LakiLaki = 'laki_laki';
    case Perempuan = 'perempuan';

    /**
     * Label berbahasa Indonesia untuk ditampilkan di UI.
     */
    public function label(): string
    {
        return match ($this) {
            self::LakiLaki => 'Laki-laki',
            self::Perempuan => 'Perempuan',
        };
    }

    /**
     * Daftar opsi {value, label} untuk mengisi dropdown di frontend.
     *
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $gender): array => [
                'value' => $gender->value,
                'label' => $gender->label(),
            ],
            self::cases(),
        );
    }
}
