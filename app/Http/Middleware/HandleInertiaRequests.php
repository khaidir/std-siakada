<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $request->user()?->only('id', 'name', 'email'),
                'role' => fn () => $request->user()?->getRoleNames()->first(),
                'roles' => fn () => $request->user()?->getRoleNames()->values(),
                'permissions' => fn () => $request->user()?->getAllPermissions()->pluck('name'),
                'profil' => fn () => $this->profil($request->user()),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }

    /**
     * Profil peran pengguna (mahasiswa atau dosen) dengan kolom spesifik.
     *
     * @return array<string, mixed>|null
     */
    protected function profil(?User $user): ?array
    {
        if (! $user) {
            return null;
        }

        if ($student = $user->student()->select(['nim', 'study_program_id', 'entry_year', 'status'])->first()) {
            return $student->only(['nim', 'study_program_id', 'entry_year', 'status']);
        }

        if ($lecturer = $user->lecturer()->select(['nidn', 'study_program_id', 'academic_rank'])->first()) {
            return $lecturer->only(['nidn', 'study_program_id', 'academic_rank']);
        }

        return null;
    }
}
