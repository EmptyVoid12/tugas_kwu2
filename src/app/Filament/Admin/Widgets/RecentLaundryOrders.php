<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Laundry;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentLaundryOrders extends BaseWidget
{
    protected static ?int $sort = -1;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Laundry::query()->with('tenant')->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('tenant.nama_laundry')
                    ->label('Tenant')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('layanan')
                    ->formatStateUsing(fn (string $state): string => Laundry::LAYANAN_OPTIONS[$state] ?? $state)
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => Laundry::STATUS_OPTIONS[$state] ?? $state)
                    ->badge(),
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('created_at')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('tracking')
                    ->icon('heroicon-o-qr-code')
                    ->url(fn (Laundry $record): string => $record->tracking_url, shouldOpenInNewTab: true),
            ])
            ->defaultPaginationPageOption(5);
    }
}
