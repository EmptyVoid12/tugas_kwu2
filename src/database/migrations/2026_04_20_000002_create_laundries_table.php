<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laundries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('nama_pelanggan');
            $table->string('no_hp', 30);
            $table->text('alamat')->nullable();
            $table->string('layanan', 50);
            $table->date('tanggal_masuk');
            $table->date('estimasi_selesai')->nullable();
            $table->string('status', 30)->default('diterima');
            $table->string('qr_code')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'tanggal_masuk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laundries');
    }
};
