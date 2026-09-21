<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGradeRequest;
use App\Models\Grade;
use App\Services\GradeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    public function index(Request $request, GradeService $service): Response
    {
        $this->authorize('viewAny', Grade::class);

        $offeringId = $request->query('offering');

        return Inertia::render('Dosen/Nilai', $service->pageData(
            $request->user(),
            $offeringId !== null ? (int) $offeringId : null,
        ));
    }

    public function store(StoreGradeRequest $request, GradeService $service): RedirectResponse
    {
        $this->authorize('create', Grade::class);

        $service->store(
            (int) $request->integer('course_offering_id'),
            $request->toBatchGradeData(),
        );

        return back()->with('success', 'Nilai berhasil disimpan.');
    }
}
