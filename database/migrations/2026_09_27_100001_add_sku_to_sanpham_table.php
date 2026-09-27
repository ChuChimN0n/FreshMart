<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('SanPham', function (Blueprint $table): void {
            $table->string('sku', 50)->nullable()->unique()->after('maSP');
        });
    }

    public function down(): void
    {
        Schema::table('SanPham', function (Blueprint $table): void {
            $table->dropUnique(['sku']);
            $table->dropColumn('sku');
        });
    }
};
