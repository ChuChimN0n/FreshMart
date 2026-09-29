<?php

namespace Database\Factories;

use App\Models\ChiTietPhieuNhap;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ChiTietPhieuNhap> */
class ChiTietPhieuNhapFactory extends Factory
{
    protected $model = ChiTietPhieuNhap::class;

    public function definition(): array
    {
        $soLuong = 5;
        $giaNhap = 20000;

        return [
            'maPN' => PhieuNhapFactory::new(),
            'maSP' => SanPhamFactory::new(),
            'soLuong' => $soLuong,
            'giaNhap' => $giaNhap,
            'thanhTien' => $soLuong * $giaNhap,
        ];
    }
}
