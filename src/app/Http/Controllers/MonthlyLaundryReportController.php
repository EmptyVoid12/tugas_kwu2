<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Services\MonthlyLaundryReportExporter;
use Illuminate\Http\Request;

class MonthlyLaundryReportController extends Controller
{
    public function __construct(
        private readonly MonthlyLaundryReportExporter $exporter,
    ) {
    }

    public function tenant(Request $request)
    {
        /** @var Tenant $tenant */
        $tenant = auth('tenant')->user();
        ['month' => $month, 'year' => $year] = $this->validatePeriod($request);

        return $this->exporter->download($tenant, $month, $year);
    }

    public function admin(Request $request, Tenant $tenant)
    {
        ['month' => $month, 'year' => $year] = $this->validatePeriod($request);

        return $this->exporter->download($tenant, $month, $year);
    }

    private function validatePeriod(Request $request): array
    {
        return $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]) + [
            'month' => now()->month,
            'year' => now()->year,
        ];
    }
}
