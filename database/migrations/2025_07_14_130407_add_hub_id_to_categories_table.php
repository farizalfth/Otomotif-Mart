<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Tambahkan kolom baru untuk menyimpan ID dari Hub UMKM
            $table->unsignedBigInteger('hub_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Hapus kolom jika migrasi di-rollback
            $table->dropColumn('hub_id');
        });
    }
};