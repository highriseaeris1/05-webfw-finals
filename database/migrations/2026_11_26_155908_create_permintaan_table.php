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
        Schema::create('permintaan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permintaan', 20)->unique();
            $table->string('kode_resi', 20)->unique();
            $table->string('nama_pemohon', 100);
            $table->string('email', 100);
            $table->string('npm_nik', 20);
            $table->string('foto_identitas', 255);
            $table->string('foto_selfie', 255);
            $table->enum('status', ['diajukan', 'disetujui', 'diambil', 'ditolak'])->default('diajukan');
            $table->text('alasan_admin')->nullable();
            $table->dateTime('tanggal_pengajuan');
            $table->foreignId('diproses_oleh')->nullable()->constrained('users');
            $table->dateTime('tanggal_diproses')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan');
    }
};
