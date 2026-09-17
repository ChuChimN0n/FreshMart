<?php

namespace Database\Factories;

use App\Models\SanPham;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SanPham> */
class SanPhamFactory extends Factory
{
    protected $model = SanPham::class;

    public function definition(): array
    {
        return [
            'maNCC' => NhaCungCapFactory::new(),
            'maDM' => DanhMucFactory::new(),
            'tenSP' => fake()->words(3, true),
            'giaBan' => 25000,
            'soLuong' => 10,
            'donVi' => 'kg',
            'trangThai' => SanPham::DANG_BAN,
        ];
    }
}
