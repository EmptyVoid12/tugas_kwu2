<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Tenant extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'nama_laundry',
        'email',
        'password',
        'alamat',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function laundries()
    {
        return $this->hasMany(Laundry::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'tenant';
    }

    public function getFilamentName(): string
    {
        return $this->nama_laundry;
    }
}
