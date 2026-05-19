<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Laundry;
use Filament\Widgets\ChartWidget;

class LaundryOrdersChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pemesanan Laundry (6 Bulan Terakhir)';
    
    // Urutan widget di dashboard
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonthsNoOverflow($i);
            $count = Laundry::query()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            // Format bulan (contoh: "Jan 2026")
            $labels[] = $month->translatedFormat('M Y');
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pemesanan',
                    'data' => $data,
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)', // Blue with opacity
                    'borderColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
