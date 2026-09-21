<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\MahasiswaMaterialService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MaterialController extends Controller
{
    public function __construct(
        private readonly MahasiswaMaterialService $materialService,
    ) {}

    /**
     * Tampilkan halaman materi kuliah untuk mahasiswa.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $offeringId = $request->integer('offering') ?: null;

        return Inertia::render('Mahasiswa/Materi', $this->materialService->pageData($user, $offeringId));
    }
}
