<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\MaterialRequest;
use App\Models\CourseMaterial;
use App\Services\MaterialService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MaterialController extends Controller
{
    public function index(Request $request, MaterialService $service): Response
    {
        $this->authorize('viewAny', CourseMaterial::class);

        $offeringId = $request->query('offering');

        return Inertia::render('Dosen/Materi', $service->pageData(
            $request->user(),
            $offeringId !== null ? (int) $offeringId : null,
        ));
    }

    public function store(MaterialRequest $request, MaterialService $service): RedirectResponse
    {
        $this->authorize('create', CourseMaterial::class);

        $service->store(
            $request->toMaterialData(),
            $request->file('file'),
        );

        return back()->with('success', 'Materi berhasil ditambahkan.');
    }

    public function update(MaterialRequest $request, MaterialService $service, int $id): RedirectResponse
    {
        $material = \App\Models\CourseMaterial::query()->findOrFail($id);

        $this->authorize('update', $material);

        $service->update(
            $id,
            $request->toMaterialData(),
            $request->file('file'),
        );

        return back()->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Request $request, MaterialService $service, int $id): RedirectResponse
    {
        $material = \App\Models\CourseMaterial::query()->findOrFail($id);

        $this->authorize('delete', $material);

        $service->destroy($id);

        return back()->with('success', 'Materi berhasil dihapus.');
    }
}
