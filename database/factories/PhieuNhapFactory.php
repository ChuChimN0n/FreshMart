<?php

namespace Database\Factories;

use App\Models\PhieuNhap;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PhieuNhap> */
class PhieuNhapFactory extends Factory
{
    protected $model = PhieuNhap::class;

    public function definition(): array
    {
        return [
            'maPhieu' => 'PN-TEST-'.$this->faker->unique()->numerify('######'),
            'maNCC' => NhaCungCapFactory::new(),
            'maNguoiTao' => TaiKhoanFactory::new(),
            'ngayTao' => now(),
            'tongTien' => 0,
            'trangThai' => PhieuNhap::NHAP,
        ];
    }
}
