<?php

use App\Models\Laundry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laundries', function (Blueprint $table) {
            $table->string('kode_tracking', 30)->nullable()->after('status');
        });

        Laundry::query()->whereNull('kode_tracking')->each(function (Laundry $laundry): void {
            $laundry->forceFill([
                'kode_tracking' => Laundry::generateTrackingCode(),
            ])->saveQuietly();
        });

        Schema::table('laundries', function (Blueprint $table) {
            $table->string('kode_tracking', 30)->nullable(false)->change();
            $table->unique('kode_tracking');
        });
    }

    public function down(): void
    {
        Schema::table('laundries', function (Blueprint $table) {
            $table->dropUnique(['kode_tracking']);
            $table->dropColumn('kode_tracking');
        });
    }
};
