<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Laundry;
use Filament\Widgets\ChartWidget;

class LaundryStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Pesanan';
    
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $statuses = [
            Laundry::STATUS_DITERIMA,
            Laundry::STATUS_DIPROSES,
            Laundry::STATUS_SELESAI,
            Laundry::STATUS_DIAMBIL,
        ];

        $counts = [];
        foreach ($statuses as $status) {
            $counts[] = Laundry::query()->where('status', $status)->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pesanan',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#eab308', // Diterima - Kuning
                        '#3b82f6', // Diproses - Biru
                        '#10b981', // Selesai - Hijau
                        '#64748b', // Diambil - Abu-abu
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['Diterima', 'Diproses', 'Selesai', 'Diambil'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // Tipe diagram doughnut
    }
}
