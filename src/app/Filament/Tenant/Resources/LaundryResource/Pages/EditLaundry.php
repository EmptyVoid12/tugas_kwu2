<?php

namespace App\Filament\Tenant\Resources\LaundryResource\Pages;

use App\Filament\Tenant\Resources\LaundryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaundry extends EditRecord
{
    protected static string $resource = LaundryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('print')
                ->label('Print')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->url(fn (): string => route('tenant.laundries.print', $this->record), shouldOpenInNewTab: true),
            Actions\DeleteAction::make(),
        ];
    }
}
