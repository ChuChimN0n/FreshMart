<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $maQuyen = DB::table('Quyen')->where('tenQuyen', 'Quản lý thông tin cá nhân')->value('maQuyen');

        if ($maQuyen !== null) {
            DB::table('VaiTroQuyen')->where('maQuyen', $maQuyen)->delete();
            DB::table('Quyen')->where('maQuyen', $maQuyen)->delete();
        }
    }

    public function down(): void
    {
        $maQuyen = DB::table('Quyen')->where('tenQuyen', 'Quản lý thông tin cá nhân')->value('maQuyen');

        if ($maQuyen === null) {
            $maQuyen = DB::table('Quyen')->insertGetId(['tenQuyen' => 'Quản lý thông tin cá nhân']);
            DB::table('VaiTroQuyen')->insert([
                ['maVT' => 3, 'maQuyen' => $maQuyen],
            ]);
        }
    }
};
