<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('laundries', function (Blueprint $table) {
            $table->decimal('berat', 8, 2)->nullable()->after('layanan');
            $table->unsignedBigInteger('total_harga')->default(0)->after('berat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laundries', function (Blueprint $table) {
            $table->dropColumn(['berat', 'total_harga']);
        });
    }
};
