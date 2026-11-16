<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['karyawan', 'admin', 'super_admin'])->default('karyawan')->after('password');
            $table->integer('jatah_cuti_tahunan')->default(12)->after('role');
            $table->integer('sisa_cuti_tahunan')->default(12)->after('jatah_cuti_tahunan');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'jatah_cuti_tahunan', 'sisa_cuti_tahunan']);
        });
    }
};
