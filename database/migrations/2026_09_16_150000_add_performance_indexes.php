<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('DonHang', function (Blueprint $table) {
            if (! Schema::hasIndex('DonHang', 'idx_donhang_trangthai_ngaydat')) {
                $table->index(['trangThai', 'ngayDat'], 'idx_donhang_trangthai_ngaydat');
            }
            if (! Schema::hasIndex('DonHang', 'idx_donhang_matk_ngaydat')) {
                $table->index(['maTK', 'ngayDat'], 'idx_donhang_matk_ngaydat');
            }
        });

        Schema::table('SanPham', function (Blueprint $table) {
            if (! Schema::hasIndex('SanPham', 'idx_sanpham_trangthai_soluong')) {
                $table->index(['trangThai', 'soLuong'], 'idx_sanpham_trangthai_soluong');
            }
        });

        Schema::table('DanhGia', function (Blueprint $table) {
            if (! Schema::hasIndex('DanhGia', 'uniq_danhgia_matk_masp')) {
                $table->unique(['maTK', 'maSP'], 'uniq_danhgia_matk_masp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('DonHang', function (Blueprint $table) {
            $table->dropIndex('idx_donhang_trangthai_ngaydat');
            $table->dropIndex('idx_donhang_matk_ngaydat');
        });

        Schema::table('SanPham', function (Blueprint $table) {
            $table->dropIndex('idx_sanpham_trangthai_soluong');
        });

        Schema::table('DanhGia', function (Blueprint $table) {
            $table->dropIndex('uniq_danhgia_matk_masp');
        });
    }
};
