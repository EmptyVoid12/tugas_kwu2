<?php

namespace App\Filament\Tenant\Resources;

use App\Filament\Tenant\Resources\LaundryResource\Pages;
use App\Models\Laundry;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class LaundryResource extends Resource
{
    protected static ?string $model = Laundry::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationGroup = 'Operasional';

    protected static ?string $recordTitleAttribute = 'nama_pelanggan';

    protected static ?int $navigationSort = -2;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Order')
                    ->schema([
                        Forms\Components\TextInput::make('nama_pelanggan')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('no_hp')
                            ->label('No HP')
                            ->tel()
                            ->required()
                            ->maxLength(30),
                        Forms\Components\Textarea::make('alamat')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('layanan')
                            ->options(Laundry::LAYANAN_OPTIONS)
                            ->native(false)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                static::syncEstimatedDate($set, $get);
                                static::syncTotalHarga($set, $get);
                                // Update label hint untuk berat
                                if ($state === Laundry::LAYANAN_DRY_CLEAN) {
                                    $set('berat', null);
                                }
                            }),
                        Forms\Components\TextInput::make('berat')
                            ->label(fn (Get $get): string => ($get('layanan') === Laundry::LAYANAN_DRY_CLEAN) ? 'Jumlah (item)' : 'Berat (kg)')
                            ->numeric()
                            ->required()
                            ->minValue(0.1)
                            ->step(0.1)
                            ->suffix(fn (Get $get): string => ($get('layanan') === Laundry::LAYANAN_DRY_CLEAN) ? 'item' : 'kg')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::syncTotalHarga($set, $get))
                            ->helperText(function (Get $get): ?string {
                                $layanan = $get('layanan');
                                if (blank($layanan)) {
                                    return 'Pilih jenis layanan terlebih dahulu';
                                }
                                $harga = Laundry::HARGA_LAYANAN[$layanan] ?? 0;
                                $satuan = Laundry::SATUAN_LAYANAN[$layanan] ?? 'kg';
                                return 'Harga: ' . Laundry::formatRupiah($harga) . ' / ' . $satuan;
                            }),
                        Forms\Components\TextInput::make('total_harga')
                            ->label('Total Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated()
                            ->default(0)
                            ->formatStateUsing(fn ($state) => $state ?? 0),
                        Forms\Components\DatePicker::make('tanggal_masuk')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(fn (Set $set, Get $get) => static::syncEstimatedDate($set, $get)),
                        Forms\Components\DatePicker::make('estimasi_selesai')
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(Laundry::STATUS_OPTIONS)
                            ->default(Laundry::STATUS_DITERIMA)
                            ->native(false)
                            ->required(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Tracking QR')
                    ->schema([
                        Forms\Components\Placeholder::make('kode_tracking')
                            ->label('Kode Tracking')
                            ->content(fn (?Laundry $record): string => $record?->kode_tracking ?? 'Kode tracking akan dibuat setelah order disimpan.')
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('tracking_url')
                            ->label('Tracking URL')
                            ->content(fn (?Laundry $record): string => $record?->tracking_url ?? 'URL akan dibuat setelah order disimpan.')
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('qr_preview')
                            ->label('Preview QR')
                            ->content(function (?Laundry $record): HtmlString {
                                if (! $record?->qr_code_url) {
                                    return new HtmlString('<span style="color:#64748b;">QR akan dibuat otomatis setelah order tersimpan.</span>');
                                }

                                return new HtmlString('<img src="' . e($record->qr_code_url) . '" alt="QR Code" style="width:180px;height:180px;border-radius:16px;border:1px solid #dbe4ee;padding:12px;background:#fff;">');
                            })
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (?Laundry $record): bool => filled($record))
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kode_tracking')
                    ->label('Kode Tracking')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No HP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('layanan')
                    ->formatStateUsing(fn (string $state): string => Laundry::LAYANAN_OPTIONS[$state] ?? $state)
                    ->badge(),
                Tables\Columns\TextColumn::make('berat')
                    ->label('Berat/Qty')
                    ->formatStateUsing(function ($state, Laundry $record): string {
                        if (blank($state)) return '-';
                        $satuan = Laundry::SATUAN_LAYANAN[$record->layanan] ?? 'kg';
                        return $state . ' ' . $satuan;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->formatStateUsing(fn ($state): string => Laundry::formatRupiah((int) ($state ?? 0)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_masuk')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('estimasi_selesai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => Laundry::STATUS_OPTIONS[$state] ?? $state)
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Laundry::STATUS_DITERIMA => 'gray',
                        Laundry::STATUS_DIPROSES => 'warning',
                        Laundry::STATUS_SELESAI => 'success',
                        Laundry::STATUS_DIAMBIL => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('qr_ready')
                    ->label('QR')
                    ->boolean()
                    ->state(fn (Laundry $record): bool => filled($record->qr_code)),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('mark_as_processed')
                    ->label('Pindah ke Diproses')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (Laundry $record): bool => $record->status === Laundry::STATUS_DITERIMA)
                    ->requiresConfirmation()
                    ->action(fn (Laundry $record) => $record->update(['status' => Laundry::STATUS_DIPROSES])),
                Tables\Actions\Action::make('mark_as_finished')
                    ->label('Pindah ke Selesai')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Laundry $record): bool => $record->status === Laundry::STATUS_DIPROSES)
                    ->requiresConfirmation()
                    ->action(fn (Laundry $record) => $record->update(['status' => Laundry::STATUS_SELESAI])),
                Tables\Actions\Action::make('mark_as_picked_up')
                    ->label('Pindah ke Diambil')
                    ->icon('heroicon-o-hand-raised')
                    ->color('info')
                    ->visible(fn (Laundry $record): bool => $record->status === Laundry::STATUS_SELESAI)
                    ->requiresConfirmation()
                    ->action(fn (Laundry $record) => $record->update(['status' => Laundry::STATUS_DIAMBIL])),
                Tables\Actions\Action::make('tracking')
                    ->label('Tracking')
                    ->icon('heroicon-o-qr-code')
                    ->color('gray')
                    ->url(fn (Laundry $record): string => $record->tracking_url, shouldOpenInNewTab: true),
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->url(fn (Laundry $record): string => route('tenant.laundries.print', $record), shouldOpenInNewTab: true),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulk_mark_as_processed')
                        ->label('Pindah ke Diproses')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update([
                                'status' => Laundry::STATUS_DIPROSES,
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('bulk_mark_as_finished')
                        ->label('Pindah ke Selesai')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update([
                                'status' => Laundry::STATUS_SELESAI,
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('bulk_mark_as_picked_up')
                        ->label('Pindah ke Diambil')
                        ->icon('heroicon-o-hand-raised')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each->update([
                                'status' => Laundry::STATUS_DIAMBIL,
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->forTenant(auth('tenant')->id());
    }

    protected static function syncEstimatedDate(Set $set, Get $get): void
    {
        $layanan = $get('layanan');
        $tanggalMasuk = $get('tanggal_masuk');

        if (blank($layanan) || blank($tanggalMasuk)) {
            return;
        }

        $estimate = Laundry::resolveEstimatedCompletionDate(
            Carbon::parse($tanggalMasuk),
            $layanan,
        );

        $set('estimasi_selesai', $estimate->format('Y-m-d'));
    }

    protected static function syncTotalHarga(Set $set, Get $get): void
    {
        $layanan = $get('layanan');
        $berat = $get('berat');

        if (blank($layanan) || blank($berat) || $berat <= 0) {
            $set('total_harga', 0);
            return;
        }

        $totalHarga = Laundry::resolvePrice($layanan, (float) $berat);
        $set('total_harga', $totalHarga);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaundries::route('/'),
            'create' => Pages\CreateLaundry::route('/create'),
            'edit' => Pages\EditLaundry::route('/{record}/edit'),
        ];
    }
}
