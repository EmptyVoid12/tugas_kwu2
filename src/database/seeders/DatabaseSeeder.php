<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            TenantSeeder::class,
        ]);

        if (filter_var(env('SEED_SAMPLE_LAUNDRIES', false), FILTER_VALIDATE_BOOL)) {
            $this->call([
                LaundrySeeder::class,
            ]);
        }
    }
}
