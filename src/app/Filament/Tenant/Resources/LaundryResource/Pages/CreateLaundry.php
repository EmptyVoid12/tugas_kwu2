<?php

namespace App\Filament\Tenant\Resources\LaundryResource\Pages;

use App\Filament\Tenant\Resources\LaundryResource;
use App\Models\Laundry;
use Filament\Resources\Pages\CreateRecord;

class CreateLaundry extends CreateRecord
{
    protected static string $resource = LaundryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = auth('tenant')->id();
        $data['status'] = $data['status'] ?? Laundry::STATUS_DITERIMA;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
