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
        'no_hp',
        'password',
        'alamat',
        'is_active',
        'subscription_ends_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'subscription_ends_at' => 'datetime',
        ];
    }

    public function laundries()
    {
        return $this->hasMany(Laundry::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'tenant') {
            return $this->hasActiveSubscription();
        }

        return false;
    }

    public function hasActiveSubscription(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        // Jika subscription_ends_at bernilai null, kita anggap belum berlangganan (atau bisa juga dianggap seumur hidup, tergantung preferensi. Di sini kita anggap harus punya masa aktif).
        if ($this->subscription_ends_at === null) {
            return false;
        }

        return now()->lessThanOrEqualTo($this->subscription_ends_at);
    }

    public function getFilamentName(): string
    {
        return $this->nama_laundry;
    }
}
