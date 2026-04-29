<?php

namespace App\Filament\Tenant\Widgets;

use App\Models\Laundry;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LaundryStatsOverview extends StatsOverviewWidget
{
    protected static ?string $pollingInterval = '10s';

    protected static ?int $sort = -3;

    protected function getStats(): array
    {
        $tenantId = auth('tenant')->id();
        $query = Laundry::query()->forTenant($tenantId);

        return [
            Stat::make('Total Order', $query->count()),
            Stat::make('Order Aktif', Laundry::query()->forTenant($tenantId)->whereIn('status', [
                Laundry::STATUS_DITERIMA,
                Laundry::STATUS_DIPROSES,
            ])->count()),
            Stat::make('Order Selesai', Laundry::query()->forTenant($tenantId)->where('status', Laundry::STATUS_SELESAI)->count()),
            Stat::make('Order Diambil', Laundry::query()->forTenant($tenantId)->where('status', Laundry::STATUS_DIAMBIL)->count()),
            Stat::make('Order Hari Ini', Laundry::query()->forTenant($tenantId)->whereDate('tanggal_masuk', today())->count()),
            Stat::make('Selesai Belum Diambil', Laundry::query()->forTenant($tenantId)->where('status', Laundry::STATUS_SELESAI)->count()),
        ];
    }
}
