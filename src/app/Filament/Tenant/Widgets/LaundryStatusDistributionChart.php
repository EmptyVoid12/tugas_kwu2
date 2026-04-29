<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Laundry;
use Filament\Widgets\DoughnutChartWidget;

class LaundryStatusDistributionChart extends DoughnutChartWidget
{
    protected static ?string $heading = 'Distribusi Status Order';

    protected static ?string $pollingInterval = '10s';

    protected function getData(): array
    {
        $tenantId = auth('tenant')->id();

        return [
            'datasets' => [
                [
                    'data' => [
                        Laundry::query()->forTenant($tenantId)->where('status', Laundry::STATUS_DITERIMA)->count(),
                        Laundry::query()->forTenant($tenantId)->where('status', Laundry::STATUS_DIPROSES)->count(),
                        Laundry::query()->forTenant($tenantId)->where('status', Laundry::STATUS_SELESAI)->count(),
                        Laundry::query()->forTenant($tenantId)->where('status', Laundry::STATUS_DIAMBIL)->count(),
                    ],
                    'backgroundColor' => [
                        '#94a3b8',
                        '#f59e0b',
                        '#10b981',
                        '#0ea5e9',
                    ],
                ],
            ],
            'labels' => [
                'Diterima',
                'Diproses',
                'Selesai',
                'Diambil',
            ],
        ];
    }
}
