<?php

namespace Database\Seeders;

use App\Models\VaiTro;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        // Seed TaiKhoan (neu chua co)
        if (DB::table('TaiKhoan')->count() === 0) {
            DB::table('TaiKhoan')->insert([
                [
                    'maVT' => VaiTro::ADMIN_ID,
                    'hoTen' => 'Admin',
                    'tenDangNhap' => 'admin',
                    'matKhau' => Hash::make('123456'),
                    'email' => 'admin@example.com',
                    'soDienThoai' => '0901234567',
                    'diaChi' => 'Ha Noi',
                    'trangThai' => 'HOAT_DONG',
                ],
                [
                    'maVT' => VaiTro::STAFF_ID,
                    'hoTen' => 'Nhan Vien 1',
                    'tenDangNhap' => 'nhanvien1',
                    'matKhau' => Hash::make('123456'),
                    'email' => 'nhanvien1@example.com',
                    'soDienThoai' => '0901234568',
                    'diaChi' => 'Ha Noi',
                    'trangThai' => 'HOAT_DONG',
                ],
                [
                    'maVT' => VaiTro::KHACH_HANG_ID,
                    'hoTen' => 'Khach Hang 1',
                    'tenDangNhap' => 'khachhang1',
                    'matKhau' => Hash::make('123456'),
                    'email' => 'khachhang1@example.com',
                    'soDienThoai' => '0901234569',
                    'diaChi' => 'Hai Phong',
                    'trangThai' => 'HOAT_DONG',
                ],
            ]);
        }

        // Seed NhaCungCap
        if (DB::table('NhaCungCap')->count() === 0) {
            DB::table('NhaCungCap')->insert([
                ['tenNCC' => 'NCC Rau Sach Tan Trieu', 'soDienThoai' => '0912345678', 'email' => 'tanttrieu@email.com', 'diaChi' => 'Hai Phong'],
                ['tenNCC' => 'NCC Rau Duoi', 'soDienThoai' => '0923456789', 'email' => 'raudoi@email.com', 'diaChi' => 'Hai Duong'],
            ]);
        }

        // Seed DanhMuc
        if (DB::table('DanhMuc')->count() === 0) {
            DB::table('DanhMuc')->insert([
                ['tenDM' => 'Rau củ'],
                ['tenDM' => 'Trái cây'],
                ['tenDM' => 'Rau thơm'],
            ]);
        }

        // Seed SanPham
        if (DB::table('SanPham')->count() === 0) {
            DB::table('SanPham')->insert([
                ['maNCC' => 1, 'maDM' => 1, 'tenSP' => 'Cà rốt', 'hinhAnh' => 'carot.jpg', 'giaBan' => 25000, 'moTa' => 'Cà rốt tự nhiên', 'soLuong' => 100, 'trangThai' => 'DANG_BAN'],
                ['maNCC' => 1, 'maDM' => 1, 'tenSP' => 'Khoai tây', 'hinhAnh' => 'khoaitay.jpg', 'giaBan' => 30000, 'moTa' => 'Khoai tây sạch', 'soLuong' => 80, 'trangThai' => 'DANG_BAN'],
                ['maNCC' => 2, 'maDM' => 2, 'tenSP' => 'Chuối', 'hinhAnh' => 'chuoi.jpg', 'giaBan' => 15000, 'moTa' => 'Chuối chín', 'soLuong' => 200, 'trangThai' => 'DANG_BAN'],
                ['maNCC' => 1, 'maDM' => 3, 'tenSP' => 'Rau muống', 'hinhAnh' => 'raumuong.jpg', 'giaBan' => 10000, 'moTa' => 'Rau muống tươi', 'soLuong' => 50, 'trangThai' => 'DANG_BAN'],
            ]);
        }

        $this->call(DemoDataSeeder::class);
    }
}
