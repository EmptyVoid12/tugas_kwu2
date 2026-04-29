<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            [
                'nama_laundry' => 'Fresh Clean Laundry',
                'email' => 'freshclean@tenant.test',
                'alamat' => 'Jl. Melati No. 12, Jakarta',
            ],
            [
                'nama_laundry' => 'Kilat Wash Express',
                'email' => 'kilatwash@tenant.test',
                'alamat' => 'Jl. Anggrek No. 8, Bandung',
            ],
        ];

        foreach ($tenants as $tenant) {
            Tenant::updateOrCreate(
                ['email' => $tenant['email']],
                [
                    'nama_laundry' => $tenant['nama_laundry'],
                    'alamat' => $tenant['alamat'],
                    'password' => Hash::make('password'),
                ],
            );
        }
    }
}
