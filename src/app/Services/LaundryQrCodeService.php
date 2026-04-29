<?php

namespace App\Services;

use App\Models\Laundry;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LaundryQrCodeService
{
    public function generateFor(Laundry $laundry): void
    {
        if (blank($laundry->tenant_id) || blank($laundry->id)) {
            return;
        }

        $directory = public_path('qrcodes');

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $relativePath = "qrcodes/{$laundry->tenant_id}_{$laundry->id}.png";
        $absolutePath = public_path($relativePath);

        if (! class_exists(QrCode::class)) {
            throw new RuntimeException('simple-qrcode package is not installed.');
        }

        File::put(
            $absolutePath,
            QrCode::format('png')->size(300)->margin(1)->generate($laundry->tracking_url),
        );

        if ($laundry->qr_code !== $relativePath) {
            $laundry->forceFill([
                'qr_code' => Str::replace('\\', '/', $relativePath),
            ])->saveQuietly();
        }
    }
}
