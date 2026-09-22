<?php

use App\Enums\Gender;

it('mendefinisikan dua jenis kelamin dengan backing value string', function () {
    expect(array_map(fn (Gender $g) => $g->value, Gender::cases()))
        ->toBe(['laki_laki', 'perempuan']);
});

it('memberi label berbahasa Indonesia', function () {
    expect(Gender::LakiLaki->label())->toBe('Laki-laki')
        ->and(Gender::Perempuan->label())->toBe('Perempuan');
});

it('menyediakan opsi siap pakai untuk dropdown', function () {
    expect(Gender::options())->toBe([
        ['value' => 'laki_laki', 'label' => 'Laki-laki'],
        ['value' => 'perempuan', 'label' => 'Perempuan'],
    ]);
});
