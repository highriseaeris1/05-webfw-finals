<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom akun tanpa mengubah atau menghapus data lama.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique();
            $table->string('role')->default('pengawas')->index();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Hapus hanya kolom yang ditambahkan migration ini.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropIndex(['role']);
            $table->dropColumn(['username', 'role', 'is_active']);
        });
    }
};
