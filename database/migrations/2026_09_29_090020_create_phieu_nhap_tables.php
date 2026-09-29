<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Luồng nhập hàng bản đầy đủ: 1 kho chung (SanPham.soLuong),
     * phiếu nhập 2 bước NHAP -> DA_XAC_NHAN, lịch sử biến động kho,
     * quan hệ N-N NCC <-> sản phẩm. Chỉ THÊM, không sửa bảng cũ.
     */
    public function up(): void
    {
        $isMysql = DB::getDriverName() === 'mysql';

        // 1. Ngưỡng tồn tối thiểu cho cảnh báo sắp hết hàng.
        if (! Schema::hasColumn('SanPham', 'mucTonToiThieu')) {
            Schema::table('SanPham', function (Blueprint $table): void {
                $table->integer('mucTonToiThieu')->default(10)->after('soLuong');
            });
            if ($isMysql) {
                DB::statement('ALTER TABLE `SanPham` ADD CHECK (`mucTonToiThieu` >= 0)');
            }
        }

        // 2. Thời điểm hoàn thành / hủy đơn cho báo cáo doanh thu.
        if (! Schema::hasColumn('DonHang', 'ngayHoanThanh')) {
            Schema::table('DonHang', function (Blueprint $table): void {
                $table->dateTime('ngayHoanThanh')->nullable()->after('trangThai');
            });
        }

        if (! Schema::hasColumn('DonHang', 'ngayHuy')) {
            Schema::table('DonHang', function (Blueprint $table): void {
                $table->dateTime('ngayHuy')->nullable()->after('ngayHoanThanh');
            });
        }

        // Backfill đơn cũ: tạm dùng ngày đặt để không mất dữ liệu thống kê.
        DB::table('DonHang')->where('trangThai', 'HOAN_THANH')->whereNull('ngayHoanThanh')
            ->update(['ngayHoanThanh' => DB::raw('ngayDat')]);
        DB::table('DonHang')->where('trangThai', 'DA_HUY')->whereNull('ngayHuy')
            ->update(['ngayHuy' => DB::raw('ngayDat')]);

        // 3. Sản phẩm mà NCC có thể cung cấp (chọn NCC -> lọc SP).
        if (! Schema::hasTable('NhaCungCapSanPham')) {
            Schema::create('NhaCungCapSanPham', function (Blueprint $table): void {
                $table->integer('maNCC');
                $table->integer('maSP');
                $table->string('trangThai', 30)->default('HOAT_DONG');
                $table->string('ghiChu', 255)->nullable();
                $table->primary(['maNCC', 'maSP']);
                $table->foreign('maNCC')->references('maNCC')->on('NhaCungCap');
                $table->foreign('maSP')->references('maSP')->on('SanPham');
                $table->index(['maSP']);
            });

            if ($isMysql) {
                DB::statement("ALTER TABLE `NhaCungCapSanPham` ADD CHECK (`trangThai` IN ('HOAT_DONG', 'NGUNG_HOAT_DONG'))");
            }

            // Khởi tạo từ dữ liệu hiện có trong SanPham.maNCC.
            $pairs = DB::table('SanPham')->select('maNCC', 'maSP')->distinct()->get()
                ->map(fn ($row): array => ['maNCC' => $row->maNCC, 'maSP' => $row->maSP, 'trangThai' => 'HOAT_DONG'])
                ->all();
            foreach (array_chunk($pairs, 500) as $chunk) {
                DB::table('NhaCungCapSanPham')->insertOrIgnore($chunk);
            }
        }

        // 4. Phiếu nhập hàng (NHAP -> DA_XAC_NHAN mới tăng tồn).
        if (! Schema::hasTable('PhieuNhap')) {
            Schema::create('PhieuNhap', function (Blueprint $table): void {
                $table->integer('maPN', true);
                $table->string('maPhieu', 30)->unique();
                $table->integer('maNCC');
                $table->integer('maNguoiTao');
                $table->dateTime('ngayTao')->useCurrent();
                $table->dateTime('ngayXacNhan')->nullable();
                $table->decimal('tongTien', 14, 2)->default(0);
                $table->string('trangThai', 30)->default('NHAP');
                $table->string('ghiChu', 500)->nullable();
                $table->foreign('maNCC')->references('maNCC')->on('NhaCungCap');
                $table->foreign('maNguoiTao')->references('maTK')->on('TaiKhoan');
                $table->index(['maNCC', 'ngayTao']);
                $table->index(['trangThai', 'ngayTao']);
                $table->index(['maNguoiTao']);
            });

            if ($isMysql) {
                DB::statement('ALTER TABLE `PhieuNhap` ADD CHECK (`tongTien` >= 0)');
                DB::statement("ALTER TABLE `PhieuNhap` ADD CHECK (`trangThai` IN ('NHAP', 'DA_XAC_NHAN'))");
            }
        }

        // 5. Chi tiết phiếu nhập.
        if (! Schema::hasTable('ChiTietPhieuNhap')) {
            Schema::create('ChiTietPhieuNhap', function (Blueprint $table): void {
                $table->integer('maCTPN', true);
                $table->integer('maPN');
                $table->integer('maSP');
                $table->integer('soLuong');
                $table->decimal('giaNhap', 12, 2);
                $table->decimal('thanhTien', 14, 2);
                $table->foreign('maPN')->references('maPN')->on('PhieuNhap')->cascadeOnDelete();
                $table->foreign('maSP')->references('maSP')->on('SanPham');
                $table->index(['maPN']);
                $table->index(['maSP']);
            });

            if ($isMysql) {
                DB::statement('ALTER TABLE `ChiTietPhieuNhap` ADD CHECK (`soLuong` > 0)');
                DB::statement('ALTER TABLE `ChiTietPhieuNhap` ADD CHECK (`giaNhap` >= 0)');
                DB::statement('ALTER TABLE `ChiTietPhieuNhap` ADD CHECK (`thanhTien` >= 0)');
            }
        }

        // 6. Lịch sử biến động kho chung.
        if (! Schema::hasTable('LichSuKho')) {
            Schema::create('LichSuKho', function (Blueprint $table): void {
                $table->bigInteger('maLSK', true);
                $table->integer('maSP');
                $table->string('loaiBienDong', 30);
                $table->integer('soLuong');
                $table->integer('tonTruoc');
                $table->integer('tonSau');
                $table->integer('maPN')->nullable();
                $table->integer('maDH')->nullable();
                $table->integer('maTK')->nullable();
                $table->dateTime('thoiGian')->useCurrent();
                $table->string('ghiChu', 255)->nullable();
                $table->foreign('maSP')->references('maSP')->on('SanPham');
                $table->foreign('maPN')->references('maPN')->on('PhieuNhap');
                $table->foreign('maDH')->references('maDH')->on('DonHang');
                $table->foreign('maTK')->references('maTK')->on('TaiKhoan');
                $table->index(['maSP', 'thoiGian']);
                $table->index(['loaiBienDong', 'thoiGian']);
                $table->index(['maPN']);
                $table->index(['maDH']);
                $table->index(['maTK']);
            });

            if ($isMysql) {
                DB::statement("ALTER TABLE `LichSuKho` ADD CHECK (`loaiBienDong` IN ('NHAP_HANG', 'BAN_HANG', 'HOAN_DON'))");
                DB::statement('ALTER TABLE `LichSuKho` ADD CHECK (`soLuong` > 0)');
                DB::statement('ALTER TABLE `LichSuKho` ADD CHECK (`tonTruoc` >= 0 AND `tonSau` >= 0)');
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('LichSuKho');
        Schema::dropIfExists('ChiTietPhieuNhap');
        Schema::dropIfExists('PhieuNhap');
        Schema::dropIfExists('NhaCungCapSanPham');

        if (Schema::hasColumn('DonHang', 'ngayHuy')) {
            Schema::table('DonHang', function (Blueprint $table): void {
                $table->dropColumn('ngayHuy');
            });
        }

        if (Schema::hasColumn('DonHang', 'ngayHoanThanh')) {
            Schema::table('DonHang', function (Blueprint $table): void {
                $table->dropColumn('ngayHoanThanh');
            });
        }

        if (Schema::hasColumn('SanPham', 'mucTonToiThieu')) {
            Schema::table('SanPham', function (Blueprint $table): void {
                $table->dropColumn('mucTonToiThieu');
            });
        }
    }
};
