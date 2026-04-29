<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Laundry;
use Carbon\Carbon;
use Filament\Widgets\LineChartWidget;

class LaundryOrdersTrendChart extends LineChartWidget
{
    protected static ?string $heading = 'Tren Order 7 Hari Terakhir';

    protected static ?string $pollingInterval = '10s';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $tenantId = auth('tenant')->id();
        $labels = [];
        $data = [];

        for ($day = 6; $day >= 0; $day--) {
            $date = Carbon::today()->subDays($day);

            $labels[] = $date->format('d M');
            $data[] = Laundry::query()
                ->forTenant($tenantId)
                ->whereDate('tanggal_masuk', $date)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Order',
                    'data' => $data,
                    'borderColor' => '#059669',
                    'backgroundColor' => 'rgba(5, 150, 105, 0.15)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
