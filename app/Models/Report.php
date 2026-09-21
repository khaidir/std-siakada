<?php

namespace App\Models;

/**
 * Model tiruan untuk otorisasi laporan & dashboard pimpinan.
 *
 * Tidak ada tabel di database — digunakan sebagai gate key
 * agar Policy (ReportPolicy) dapat didaftarkan via Gate::policy.
 */
class Report
{
    //
}
