<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel master kategori barang temuan.
     */
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table): void {
            $table->id();
            $table->string('nama_kategori', 50)->unique();
        });
    }

    /**
     * Hapus tabel kategori saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
