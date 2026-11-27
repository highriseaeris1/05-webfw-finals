<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel master barang temuan.
     */
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete();
            $table->string('nama_barang', 100);
            $table->string('nama_penemu', 100);
            $table->date('tanggal_ditemukan');
            $table->text('deskripsi');
            // Path foto bersifat opsional sementara menunggu modul unggah berkas (W10 - Ibnu).
            $table->string('foto', 255)->nullable();
            $table->enum('status', ['tersedia', 'diajukan', 'terambil', 'dimusnahkan'])->default('tersedia');
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Hapus tabel barang saat rollback.
     */
    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
};
