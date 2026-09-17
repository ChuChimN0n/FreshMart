<?php

namespace Database\Factories;

use App\Models\ChiTietDonHang;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ChiTietDonHang> */
class ChiTietDonHangFactory extends Factory
{
    protected $model = ChiTietDonHang::class;

    public function definition(): array
    {
        return ['maDH' => DonHangFactory::new(), 'maSP' => SanPhamFactory::new(), 'soLuong' => 2, 'donGia' => 25000, 'thanhTien' => 50000];
    }
}
