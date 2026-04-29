<?php

namespace App\Filament\Tenant\Resources\LaundryResource\Pages;

use App\Filament\Tenant\Resources\LaundryResource;
use App\Models\Laundry;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Carbon;

class ListLaundries extends ListRecords
{
    protected static string $resource = LaundryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('download_monthly_report')
                ->label('Unduh Laporan Bulanan')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->form([
                    Forms\Components\DatePicker::make('period')
                        ->label('Pilih Tanggal dalam Bulan Laporan')
                        ->default(now())
                        ->required()
                        ->native(false)
                        ->closeOnDateSelection()
                        ->maxDate(now()),
                ])
                ->action(function (array $data): void {
                    $period = Carbon::parse($data['period']);

                    $this->redirect(route('tenant.reports.monthly', [
                        'month' => $period->month,
                        'year' => $period->year,
                    ]), navigate: false);
                }),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tenantId = auth('tenant')->id();

        return [
            'all' => Tab::make('Semua')
                ->badge(Laundry::query()->forTenant($tenantId)->count()),
            Laundry::STATUS_DITERIMA => Tab::make('Diterima')
                ->badge(Laundry::query()->forTenant($tenantId)->where('status', Laundry::STATUS_DITERIMA)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', Laundry::STATUS_DITERIMA)),
            Laundry::STATUS_DIPROSES => Tab::make('Diproses')
                ->badge(Laundry::query()->forTenant($tenantId)->where('status', Laundry::STATUS_DIPROSES)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', Laundry::STATUS_DIPROSES)),
            Laundry::STATUS_SELESAI => Tab::make('Selesai')
                ->badge(Laundry::query()->forTenant($tenantId)->where('status', Laundry::STATUS_SELESAI)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', Laundry::STATUS_SELESAI)),
            Laundry::STATUS_DIAMBIL => Tab::make('Diambil')
                ->badge(Laundry::query()->forTenant($tenantId)->where('status', Laundry::STATUS_DIAMBIL)->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', Laundry::STATUS_DIAMBIL)),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'all';
    }
}
