<?php

namespace App\Http\Controllers\Mahasiswa;

use App\DTO\StudentProfileData;
use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStudentProfileRequest;
use App\Services\StudentProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfileController extends Controller
{
    public function __construct(
        protected StudentProfileService $profiles,
    ) {}

    /**
     * Tampilkan halaman pengaturan profil mahasiswa.
     */
    public function index(Request $request): Response
    {
        $profile = $this->profiles->profileFor((int) $request->user()->id);

        if ($profile === null) {
            throw new NotFoundHttpException('Profil mahasiswa tidak ditemukan.');
        }

        return Inertia::render('Mahasiswa/Profile', [
            'profile' => $profile,
            'genderOptions' => Gender::options(),
        ]);
    }

    /**
     * Simpan perubahan biodata mahasiswa.
     */
    public function update(UpdateStudentProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $student = $user->student;

        $this->authorize('update', $student);

        $data = StudentProfileData::from($request->validated());

        $this->profiles->update($user, (int) $student->id, $data);

        return redirect()
            ->route('mahasiswa.profil.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }
}
