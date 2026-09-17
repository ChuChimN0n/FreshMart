<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['VaiTro', 'Quyen', 'VaiTroQuyen', 'TaiKhoan', 'NhaCungCap', 'DanhMuc', 'SanPham', 'GioHang', 'ChiTietGioHang', 'DonHang', 'ChiTietDonHang', 'DanhGia'];
        $existing = array_filter($tables, fn (string $table): bool => Schema::hasTable($table));

        if (count($existing) === count($tables)) {
            return;
        }

        if ($existing !== []) {
            throw new RuntimeException('Schema cửa hàng chưa đầy đủ. Cần đối chiếu các bảng hiện có trước khi migrate.');
        }

        Schema::create('VaiTro', function (Blueprint $table): void {
            $table->integer('maVT', true);
            $table->string('tenVT', 100)->unique();
            $table->string('moTa')->nullable();
        });
        Schema::create('Quyen', function (Blueprint $table): void {
            $table->integer('maQuyen', true);
            $table->string('tenQuyen', 100)->unique();
        });
        Schema::create('VaiTroQuyen', function (Blueprint $table): void {
            $table->integer('maVT');
            $table->integer('maQuyen');
            $table->primary(['maVT', 'maQuyen']);
            $table->foreign('maVT')->references('maVT')->on('VaiTro');
            $table->foreign('maQuyen')->references('maQuyen')->on('Quyen');
        });
        Schema::create('TaiKhoan', function (Blueprint $table): void {
            $table->integer('maTK', true);
            $table->integer('maVT');
            $table->string('hoTen', 100);
            $table->string('tenDangNhap', 100)->unique();
            $table->string('matKhau');
            $table->string('email', 100)->unique();
            $table->string('soDienThoai', 15)->nullable();
            $table->string('diaChi')->nullable();
            $table->string('trangThai', 50)->default('HOAT_DONG');
            $table->foreign('maVT')->references('maVT')->on('VaiTro');
        });
        Schema::create('NhaCungCap', function (Blueprint $table): void {
            $table->integer('maNCC', true);
            $table->string('tenNCC', 100);
            $table->string('soDienThoai', 15);
            $table->string('email', 100)->nullable();
            $table->string('diaChi')->nullable();
        });
        Schema::create('DanhMuc', function (Blueprint $table): void {
            $table->integer('maDM', true);
            $table->string('tenDM', 100);
        });
        Schema::create('SanPham', function (Blueprint $table): void {
            $table->integer('maSP', true);
            $table->integer('maNCC');
            $table->integer('maDM');
            $table->string('tenSP', 150);
            $table->string('hinhAnh')->nullable();
            $table->decimal('giaBan', 12, 2);
            $table->text('moTa')->nullable();
            $table->integer('soLuong')->default(0);
            $table->string('trangThai', 50)->default('DANG_BAN');
            $table->foreign('maNCC')->references('maNCC')->on('NhaCungCap');
            $table->foreign('maDM')->references('maDM')->on('DanhMuc');
        });
        Schema::create('GioHang', function (Blueprint $table): void {
            $table->integer('maGioHang', true);
            $table->integer('maTK')->unique();
            $table->decimal('tongTien', 12, 2)->default(0);
            $table->foreign('maTK')->references('maTK')->on('TaiKhoan');
        });
        Schema::create('DonHang', function (Blueprint $table): void {
            $table->integer('maDH', true);
            $table->integer('maTK');
            $table->dateTime('ngayDat')->useCurrent();
            $table->string('tenNguoiNhan', 100);
            $table->string('soDienThoai', 15);
            $table->string('diaChi');
            $table->decimal('tongTien', 12, 2)->default(0);
            $table->string('trangThai', 50)->default('CHO_XAC_NHAN');
            $table->foreign('maTK')->references('maTK')->on('TaiKhoan');
        });
        foreach (['ChiTietGioHang' => ['maCTGH', 'maGioHang', 'GioHang'], 'ChiTietDonHang' => ['maCTDH', 'maDH', 'DonHang']] as $name => [$key, $parentKey, $parentTable]) {
            Schema::create($name, function (Blueprint $table) use ($key, $parentKey, $parentTable): void {
                $table->integer($key, true);
                $table->integer($parentKey);
                $table->integer('maSP');
                $table->integer('soLuong');
                $table->decimal('donGia', 12, 2);
                $table->decimal('thanhTien', 12, 2);
                $table->foreign($parentKey)->references($parentKey)->on($parentTable);
                $table->foreign('maSP')->references('maSP')->on('SanPham');
            });
        }
        Schema::create('DanhGia', function (Blueprint $table): void {
            $table->integer('maDanhGia', true);
            $table->integer('maTK');
            $table->integer('maSP');
            $table->integer('soSao');
            $table->text('noiDung')->nullable();
            $table->foreign('maTK')->references('maTK')->on('TaiKhoan');
            $table->foreign('maSP')->references('maSP')->on('SanPham');
        });

        if (DB::getDriverName() === 'mysql') {
            foreach ([
                'SanPham' => ['giaBan >= 0', 'soLuong >= 0'],
                'GioHang' => ['tongTien >= 0'],
                'DonHang' => ['tongTien >= 0'],
                'ChiTietGioHang' => ['soLuong > 0', 'donGia >= 0', 'thanhTien >= 0'],
                'ChiTietDonHang' => ['soLuong > 0', 'donGia >= 0', 'thanhTien >= 0'],
                'DanhGia' => ['soSao BETWEEN 1 AND 5'],
            ] as $table => $checks) {
                foreach ($checks as $check) {
                    DB::statement("ALTER TABLE `{$table}` ADD CHECK ({$check})");
                }
            }
        }
    }

    /**
     * This baseline may adopt existing tables; dropping them would destroy imported data.
     */
    public function down(): void
    {
        throw new RuntimeException('Không rollback schema nền đã tiếp nhận dữ liệu. Dùng migration sửa tiếp theo.');
    }
};
