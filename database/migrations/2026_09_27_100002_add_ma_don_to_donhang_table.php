<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('DonHang', function (Blueprint $table): void {
            $table->string('maDon', 20)->nullable()->unique()->after('maDH');
        });
    }

    public function down(): void
    {
        Schema::table('DonHang', function (Blueprint $table): void {
            $table->dropUnique(['maDon']);
            $table->dropColumn('maDon');
        });
    }
};
