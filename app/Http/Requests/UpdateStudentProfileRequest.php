<?php

namespace App\Http\Requests;

use App\Enums\Gender;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateStudentProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $student = $this->user()?->student;

        return $student !== null && $this->user()->can('update', $student);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                // Abaikan baris milik pengguna ini sendiri agar email lamanya tetap valid.
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
            'gender' => ['nullable', new Enum(Gender::class)],
            'address' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email tersebut sudah digunakan pengguna lain.',
            'birth_date.date' => 'Tanggal lahir tidak valid.',
            'birth_date.before' => 'Tanggal lahir tidak boleh hari ini atau di masa depan.',
            'birth_date.after' => 'Tanggal lahir tidak masuk akal.',
            'gender.Illuminate\\Validation\\Rules\\Enum' => 'Jenis kelamin tidak valid.',
            'address.max' => 'Alamat maksimal 1000 karakter.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'phone.regex' => 'Nomor telepon hanya boleh berisi angka dan tanda + - ( ).',
        ];
    }
}
