<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menyimpan tahun terakhir cuti di-reset (default tahun sekarang)
            $table->integer('tahun_cuti_terakhir')->default(now()->year)->after('sisa_cuti_tahunan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tahun_cuti_terakhir');
        });
    }
};