<?php

namespace App\Filament\Admin\Resources\TenantResource\Pages;

use App\Filament\Admin\Resources\TenantResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

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

                    $this->redirect(route('admin.tenants.reports.monthly', [
                        'tenant' => $this->getRecord(),
                        'month' => $period->month,
                        'year' => $period->year,
                    ]), navigate: false);
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
