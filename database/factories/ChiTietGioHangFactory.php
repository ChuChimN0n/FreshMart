<?php

namespace Database\Factories;

use App\Models\ChiTietGioHang;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ChiTietGioHang> */
class ChiTietGioHangFactory extends Factory
{
    protected $model = ChiTietGioHang::class;

    public function definition(): array
    {
        return ['maSP' => SanPhamFactory::new(), 'soLuong' => 2, 'donGia' => 25000, 'thanhTien' => 50000];
    }
}
