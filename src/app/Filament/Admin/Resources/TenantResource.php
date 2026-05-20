<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Platform';

    protected static ?string $recordTitleAttribute = 'nama_laundry';

    protected static ?int $navigationSort = -3;

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_laundry')
                    ->label('Nama Laundry')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('no_hp')
                    ->label('No HP')
                    ->tel()
                    ->required()
                    ->maxLength(30),
                Forms\Components\Textarea::make('alamat')
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\Section::make('Subscription & Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif (Bisa Akses Panel)')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('subscription_ends_at')
                            ->label('Batas Waktu Langganan')
                            ->helperText('Tenant hanya bisa login jika toggle aktif dan masa langganan belum lewat.')
                            ->native(false),
                    ])->columns(2),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->same('password_confirmation'),
                Forms\Components\TextInput::make('password_confirmation')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_laundry')
                    ->label('Nama Laundry')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->label('No HP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription_ends_at')
                    ->label('Langganan Berakhir')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('laundries_count')
                    ->counts('laundries')
                    ->label('Total Order')
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('download_monthly_report')
                    ->label('Laporan Bulanan')
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
                    ->action(function (Tenant $record, array $data) {
                        $period = Carbon::parse($data['period']);

                        return redirect()->route('admin.tenants.reports.monthly', [
                            'tenant' => $record,
                            'month' => $period->month,
                            'year' => $period->year,
                        ]);
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}
