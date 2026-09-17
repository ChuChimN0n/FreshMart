<?php

namespace Database\Factories;

use App\Models\DonHang;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DonHang> */
class DonHangFactory extends Factory
{
    protected $model = DonHang::class;

    public function definition(): array
    {
        return [
            'maTK' => TaiKhoanFactory::new(),
            'ngayDat' => now(),
            'tenNguoiNhan' => fake()->name(),
            'soDienThoai' => '0901234567',
            'diaChi' => 'Hà Nội',
            'tongTien' => 50000,
            'trangThai' => DonHang::CHO_XAC_NHAN,
        ];
    }
}
