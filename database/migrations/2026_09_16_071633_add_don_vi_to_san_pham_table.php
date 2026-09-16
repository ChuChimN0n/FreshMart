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
        Schema::table('SanPham', function (Blueprint $table) {
            $table->string('donVi', 20)->default('kg')->after('hinhAnh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('SanPham', function (Blueprint $table) {
            $table->dropColumn('donVi');
        });
    }
};
