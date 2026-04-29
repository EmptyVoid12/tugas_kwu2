<?php

namespace Database\Seeders;

use App\Models\Laundry;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class LaundrySeeder extends Seeder
{
    public function run(): void
    {
        $statuses = array_keys(Laundry::STATUS_OPTIONS);
        $layanans = array_keys(Laundry::LAYANAN_OPTIONS);

        Tenant::query()->each(function (Tenant $tenant) use ($statuses, $layanans): void {
            if ($tenant->laundries()->exists()) {
                return;
            }

            foreach (range(1, 12) as $index) {
                $tanggalMasuk = Carbon::today()->subDays(fake()->numberBetween(0, 6));
                $layanan = fake()->randomElement($layanans);

                $tenant->laundries()->create([
                    'nama_pelanggan' => fake()->name(),
                    'no_hp' => fake()->phoneNumber(),
                    'alamat' => fake()->address(),
                    'layanan' => $layanan,
                    'tanggal_masuk' => $tanggalMasuk,
                    'estimasi_selesai' => Laundry::resolveEstimatedCompletionDate($tanggalMasuk, $layanan),
                    'status' => fake()->randomElement($statuses),
                ]);
            }
        });
    }
}
