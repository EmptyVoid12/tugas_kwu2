<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Tenant;
use Filament\Widgets\ChartWidget;

class TenantStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Berlangganan Tenant';
    
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        // Hitung tenant aktif
        $activeCount = Tenant::where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('subscription_ends_at')
                      ->orWhere('subscription_ends_at', '>=', now());
            })->count();

        // Hitung tenant tidak aktif / expired
        $inactiveCount = Tenant::where('is_active', false)
            ->orWhere(function ($query) {
                $query->whereNotNull('subscription_ends_at')
                      ->where('subscription_ends_at', '<', now());
            })->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Tenant',
                    'data' => [$activeCount, $inactiveCount],
                    'backgroundColor' => [
                        '#10b981', // Aktif - Hijau
                        '#ef4444', // Tidak Aktif - Merah
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['Aktif', 'Tidak Aktif / Kedaluwarsa'],
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // Tipe diagram pie
    }
}
