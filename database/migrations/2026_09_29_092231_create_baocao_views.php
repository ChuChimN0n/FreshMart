<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Views phục vụ báo cáo nhập hàng / tồn kho / doanh thu / lịch sử kho.
     * Tách riêng để dễ rollback khi sửa view.
     */
    public function up(): void
    {
        $this->dropViews();

        DB::statement(<<<'SQL'
            CREATE VIEW `vw_baocao_nhaphang` AS
            SELECT
                pn.`maPN`, pn.`maPhieu`, pn.`ngayTao`, pn.`ngayXacNhan`, pn.`trangThai`,
                ncc.`maNCC`, ncc.`codeNCC`, ncc.`tenNCC`,
                sp.`maSP`, sp.`sku`, sp.`tenSP`,
                ctpn.`soLuong`, ctpn.`giaNhap`, ctpn.`thanhTien`,
                pn.`tongTien`
            FROM `PhieuNhap` pn
            JOIN `NhaCungCap` ncc ON ncc.`maNCC` = pn.`maNCC`
            JOIN `ChiTietPhieuNhap` ctpn ON ctpn.`maPN` = pn.`maPN`
            JOIN `SanPham` sp ON sp.`maSP` = ctpn.`maSP`
            WHERE pn.`trangThai` = 'DA_XAC_NHAN'
            SQL);

        DB::statement(<<<'SQL'
            CREATE VIEW `vw_baocao_tonkho` AS
            SELECT
                sp.`maSP`, sp.`sku`, sp.`tenSP`, sp.`donVi`,
                sp.`soLuong` AS `soLuongTon`,
                sp.`mucTonToiThieu`, sp.`trangThai`,
                CASE
                    WHEN sp.`soLuong` = 0 THEN 'HET_HANG'
                    WHEN sp.`soLuong` <= sp.`mucTonToiThieu` THEN 'SAP_HET_HANG'
                    ELSE 'CON_HANG'
                END AS `trangThaiTon`
            FROM `SanPham` sp
            SQL);

        DB::statement(<<<'SQL'
            CREATE VIEW `vw_canhbao_tonkho` AS
            SELECT * FROM `vw_baocao_tonkho`
            WHERE `trangThaiTon` IN ('HET_HANG', 'SAP_HET_HANG')
            SQL);

        DB::statement(<<<'SQL'
            CREATE VIEW `vw_thongke_doanhthu` AS
            SELECT
                dh.`maDH`, dh.`maDon`, dh.`maTK`,
                dh.`ngayDat`, dh.`ngayHoanThanh`,
                dh.`tongTien` AS `doanhThu`
            FROM `DonHang` dh
            WHERE dh.`trangThai` = 'HOAN_THANH'
            SQL);

        DB::statement(<<<'SQL'
            CREATE VIEW `vw_doanhthu_theongay` AS
            SELECT
                DATE(COALESCE(`ngayHoanThanh`, `ngayDat`)) AS `ngay`,
                COUNT(*) AS `soDonHoanThanh`,
                SUM(`tongTien`) AS `tongDoanhThu`
            FROM `DonHang`
            WHERE `trangThai` = 'HOAN_THANH'
            GROUP BY DATE(COALESCE(`ngayHoanThanh`, `ngayDat`))
            SQL);

        DB::statement(<<<'SQL'
            CREATE VIEW `vw_lichsukho` AS
            SELECT
                lsk.`maLSK`, lsk.`thoiGian`, lsk.`loaiBienDong`,
                sp.`maSP`, sp.`sku`, sp.`tenSP`,
                lsk.`soLuong`, lsk.`tonTruoc`, lsk.`tonSau`,
                lsk.`maPN`, lsk.`maDH`, lsk.`maTK`, lsk.`ghiChu`
            FROM `LichSuKho` lsk
            JOIN `SanPham` sp ON sp.`maSP` = lsk.`maSP`
            SQL);
    }

    public function down(): void
    {
        $this->dropViews();
    }

    private function dropViews(): void
    {
        foreach ([
            'vw_lichsukho',
            'vw_doanhthu_theongay',
            'vw_thongke_doanhthu',
            'vw_canhbao_tonkho',
            'vw_baocao_tonkho',
            'vw_baocao_nhaphang',
        ] as $view) {
            DB::statement("DROP VIEW IF EXISTS `{$view}`");
        }
    }
};
