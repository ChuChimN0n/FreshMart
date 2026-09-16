<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\TaiKhoan;
use App\Models\VaiTro;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function adminDashboard()
    {
        $productStats = SanPham::select(
            DB::raw('COUNT(*) AS total'),
            DB::raw('SUM(CASE WHEN trangThai = "'.SanPham::DANG_BAN.'" AND soLuong > 0 THEN 1 ELSE 0 END) AS in_stock')
        )->first();

        $donHangStats = DonHang::select(
            'trangThai',
            DB::raw('COUNT(*) AS cnt'),
            DB::raw('SUM(CASE WHEN trangThai IN ("'.DonHang::DA_XAC_NHAN.'","'.DonHang::DANG_GIAO.'","'.DonHang::HOAN_THANH.'") THEN tongTien ELSE 0 END) AS revenue')
        )->groupBy('trangThai')->get();

        $tongSanPham = $productStats->total;
        $sanPhamConHang = $productStats->in_stock;
        $tongDonHang = $donHangStats->sum('cnt');
        $donChoXacNhan = $donHangStats->firstWhere('trangThai', DonHang::CHO_XAC_NHAN)?->cnt ?? 0;
        $tongKhachHang = TaiKhoan::where('maVT', VaiTro::KHACH_HANG_ID)->count();
        $doanhThu = $donHangStats->sum('revenue');

        $donHangMoiNhat = DonHang::with('taiKhoan')->orderByDesc('ngayDat')->limit(5)->get();

        return view('admin.dashboard', compact(
            'tongSanPham', 'sanPhamConHang', 'tongDonHang', 'donChoXacNhan',
            'tongKhachHang', 'doanhThu', 'donHangMoiNhat'
        ));
    }

    public function staffDashboard()
    {
        $productStats = SanPham::select(
            DB::raw('COUNT(*) AS total'),
            DB::raw('SUM(CASE WHEN trangThai = "'.SanPham::DANG_BAN.'" AND soLuong > 0 THEN 1 ELSE 0 END) AS in_stock')
        )->first();

        $donHangStats = DonHang::select(
            'trangThai',
            DB::raw('COUNT(*) AS cnt')
        )->groupBy('trangThai')->get();

        $tongSanPham = $productStats->total;
        $sanPhamConHang = $productStats->in_stock;
        $tongDonHang = $donHangStats->sum('cnt');
        $donChoXacNhan = $donHangStats->firstWhere('trangThai', DonHang::CHO_XAC_NHAN)?->cnt ?? 0;

        $donHangMoiNhat = DonHang::with('taiKhoan')->orderByDesc('ngayDat')->limit(5)->get();

        return view('staff.dashboard', compact(
            'tongSanPham', 'sanPhamConHang', 'tongDonHang', 'donChoXacNhan', 'donHangMoiNhat'
        ));
    }
}
