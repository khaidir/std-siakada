<?php

namespace App\Http\Controllers\Pimpinan;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {}

    /**
     * Tampilkan dashboard eksekutif pimpinan.
     */
    public function index(): Response
    {
        $this->authorize('view', \App\Models\Report::class);

        return Inertia::render('pimpinan/Dashboard', $this->reportService->dashboardData());
    }
}
