<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('NhaCungCap', function (Blueprint $table): void {
            $table->string('codeNCC', 20)->nullable()->unique()->after('maNCC');
        });
    }

    public function down(): void
    {
        Schema::table('NhaCungCap', function (Blueprint $table): void {
            $table->dropUnique(['codeNCC']);
            $table->dropColumn('codeNCC');
        });
    }
};
