<?php

namespace Database\Factories;

use App\Models\TaiKhoan;
use App\Models\VaiTro;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TaiKhoan> */
class TaiKhoanFactory extends Factory
{
    protected $model = TaiKhoan::class;

    public function definition(): array
    {
        return [
            'maVT' => VaiTro::KHACH_HANG_ID,
            'hoTen' => fake()->name(),
            'tenDangNhap' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'matKhau' => 'password123',
            'soDienThoai' => '0901234567',
            'diaChi' => 'Hà Nội',
            'trangThai' => 'HOAT_DONG',
        ];
    }

    public function admin(): static
    {
        return $this->state(['maVT' => VaiTro::ADMIN_ID]);
    }

    public function staff(): static
    {
        return $this->state(['maVT' => VaiTro::STAFF_ID]);
    }

    public function locked(): static
    {
        return $this->state(['trangThai' => 'KHOA']);
    }
}
