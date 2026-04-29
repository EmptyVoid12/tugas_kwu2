<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Laundry;
use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Tenant', Tenant::count()),
            Stat::make('Total Order', Laundry::count()),
            Stat::make('Order Aktif', Laundry::query()->whereIn('status', [
                Laundry::STATUS_DITERIMA,
                Laundry::STATUS_DIPROSES,
            ])->count()),
            Stat::make('Selesai Belum Diambil', Laundry::query()->where('status', Laundry::STATUS_SELESAI)->count()),
        ];
    }
}
