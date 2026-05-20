<?php

namespace App\Models;

use App\Services\LaundryQrCodeService;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Laundry extends Model
{
    use HasFactory;

    public const STATUS_DITERIMA = 'diterima';
    public const STATUS_DIPROSES = 'diproses';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIAMBIL = 'diambil';

    public const LAYANAN_REGULER = 'reguler';
    public const LAYANAN_EXPRESS = 'express';
    public const LAYANAN_KILAT = 'kilat';
    public const LAYANAN_DRY_CLEAN = 'dry_clean';

    public const STATUS_OPTIONS = [
        self::STATUS_DITERIMA => 'Diterima',
        self::STATUS_DIPROSES => 'Diproses',
        self::STATUS_SELESAI => 'Selesai',
        self::STATUS_DIAMBIL => 'Diambil',
    ];

    public const LAYANAN_OPTIONS = [
        self::LAYANAN_REGULER => 'Reguler (2-3 hari)',
        self::LAYANAN_EXPRESS => 'Express (1 hari)',
        self::LAYANAN_KILAT => 'Kilat (beberapa jam)',
        self::LAYANAN_DRY_CLEAN => 'Dry Clean',
    ];

    /**
     * Harga per kg/item untuk setiap layanan.
     */
    public const HARGA_LAYANAN = [
        self::LAYANAN_REGULER => 7000,    // per kg
        self::LAYANAN_EXPRESS => 15000,   // per kg
        self::LAYANAN_KILAT => 25000,     // per kg
        self::LAYANAN_DRY_CLEAN => 35000, // per item
    ];

    /**
     * Label satuan untuk setiap layanan.
     */
    public const SATUAN_LAYANAN = [
        self::LAYANAN_REGULER => 'kg',
        self::LAYANAN_EXPRESS => 'kg',
        self::LAYANAN_KILAT => 'kg',
        self::LAYANAN_DRY_CLEAN => 'item',
    ];

    protected $fillable = [
        'nama_pelanggan',
        'no_hp',
        'alamat',
        'layanan',
        'berat',
        'total_harga',
        'tanggal_masuk',
        'estimasi_selesai',
        'status',
        'kode_tracking',
        'qr_code',
    ];

    protected $appends = [
        'tracking_url',
        'qr_code_url',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'estimasi_selesai' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Laundry $laundry): void {
            if (! app()->runningInConsole() && ! Auth::guard('tenant')->check()) {
                throw new AuthorizationException('Order laundry hanya boleh dibuat oleh tenant yang sedang login.');
            }

            if (blank($laundry->tenant_id) && Auth::guard('tenant')->check()) {
                $laundry->tenant_id = Auth::guard('tenant')->id();
            }

            if (blank($laundry->tenant_id)) {
                throw new AuthorizationException('Tenant order tidak valid.');
            }

            if (blank($laundry->status)) {
                $laundry->status = self::STATUS_DITERIMA;
            }

            if (blank($laundry->estimasi_selesai) && filled($laundry->tanggal_masuk) && filled($laundry->layanan)) {
                $laundry->estimasi_selesai = self::resolveEstimatedCompletionDate(
                    CarbonImmutable::parse($laundry->tanggal_masuk),
                    $laundry->layanan,
                );
            }

            // Auto-calculate total harga jika belum diset
            if (filled($laundry->layanan) && filled($laundry->berat) && blank($laundry->total_harga)) {
                $laundry->total_harga = self::resolvePrice($laundry->layanan, $laundry->berat);
            }

            if (blank($laundry->kode_tracking)) {
                $laundry->kode_tracking = self::generateTrackingCode();
            }
        });

        static::created(function (Laundry $laundry): void {
            app(LaundryQrCodeService::class)->generateFor($laundry);
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeForTenant(Builder $query, ?int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function getTrackingUrlAttribute(): string
    {
        return route('tracking.code', $this->kode_tracking);
    }

    public function getQrCodeUrlAttribute(): ?string
    {
        if (blank($this->qr_code)) {
            return null;
        }

        return asset($this->qr_code);
    }

    public static function resolveEstimatedCompletionDate(CarbonInterface $tanggalMasuk, string $layanan): CarbonInterface
    {
        return match ($layanan) {
            self::LAYANAN_EXPRESS => $tanggalMasuk->copy()->addDay(),
            self::LAYANAN_KILAT => $tanggalMasuk->copy(),
            self::LAYANAN_DRY_CLEAN => $tanggalMasuk->copy()->addDays(2),
            default => $tanggalMasuk->copy()->addDays(3),
        };
    }

    /**
     * Hitung total harga berdasarkan layanan dan berat/jumlah.
     */
    public static function resolvePrice(string $layanan, float $beratOrJumlah): int
    {
        $hargaSatuan = self::HARGA_LAYANAN[$layanan] ?? 0;

        return (int) ceil($hargaSatuan * $beratOrJumlah);
    }

    /**
     * Format rupiah helper.
     */
    public static function formatRupiah(int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public static function generateTrackingCode(): string
    {
        do {
            $code = 'LDR-' . Str::upper(Str::random(8));
        } while (self::query()->where('kode_tracking', $code)->exists());

        return $code;
    }
}
