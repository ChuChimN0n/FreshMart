<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\SanPham;
use App\Models\TaiKhoan;
use App\Models\VaiTro;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Doanh thu 14 ngày gần nhất (ngày thiếu fill 0).
     *
     * @return array{ngay: string, tien: float}[]
     */
    private function doanhThu14Ngay(): array
    {
        $tuNgay = Carbon::today()->subDays(13)->toDateString();
        $raw = DonHang::select(
            DB::raw('DATE(ngayDat) AS ngay'),
            DB::raw('SUM(tongTien) AS tien')
        )
            ->whereIn('trangThai', [DonHang::DA_XAC_NHAN, DonHang::DANG_GIAO, DonHang::HOAN_THANH])
            ->whereDate('ngayDat', '>=', $tuNgay)
            ->groupBy(DB::raw('DATE(ngayDat)'))
            ->pluck('tien', 'ngay')
            ->all();

        $chart = [];
        for ($i = 13; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $chart[] = [
                'ngay' => $day->format('d/m'),
                'tien' => (float) ($raw[$day->toDateString()] ?? 0),
            ];
        }

        return $chart;
    }

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

        $donCanXuLy = DonHang::with('taiKhoan')
            ->where('trangThai', DonHang::CHO_XAC_NHAN)
            ->orderByDesc('ngayDat')
            ->limit(5)
            ->get();

        $spSapHet = SanPham::with('danhMuc')
            ->whereBetween('soLuong', [1, 5])
            ->orderBy('soLuong')
            ->limit(5)
            ->get();

        $coCauDon = $donHangStats->pluck('cnt', 'trangThai')->all();

        $dauThangNay = Carbon::now()->startOfMonth()->toDateString();
        $doanhThuThangNay = (float) DonHang::whereIn('trangThai', [DonHang::DA_XAC_NHAN, DonHang::DANG_GIAO, DonHang::HOAN_THANH])
            ->whereDate('ngayDat', '>=', $dauThangNay)
            ->sum('tongTien');
        $dauThangTruoc = Carbon::now()->subMonthNoOverflow()->startOfMonth()->toDateString();
        $cuoiThangTruoc = Carbon::now()->startOfMonth()->subDay()->toDateString();
        $doanhThuThangTruoc = (float) DonHang::whereIn('trangThai', [DonHang::DA_XAC_NHAN, DonHang::DANG_GIAO, DonHang::HOAN_THANH])
            ->whereDate('ngayDat', '>=', $dauThangTruoc)
            ->whereDate('ngayDat', '<=', $cuoiThangTruoc)
            ->sum('tongTien');
        $tangTruong = $doanhThuThangTruoc > 0
            ? round(($doanhThuThangNay - $doanhThuThangTruoc) / $doanhThuThangTruoc * 100, 1)
            : null;

        return view('admin.dashboard', compact(
            'tongSanPham', 'sanPhamConHang', 'tongDonHang', 'donChoXacNhan',
            'tongKhachHang', 'doanhThu', 'donHangMoiNhat',
            'donCanXuLy', 'spSapHet', 'coCauDon', 'tangTruong'
        ) + ['doanhThu14Ngay' => $this->doanhThu14Ngay()]);
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

        $donCanXuLy = DonHang::with('taiKhoan')
            ->where('trangThai', DonHang::CHO_XAC_NHAN)
            ->orderByDesc('ngayDat')
            ->limit(5)
            ->get();

        $spSapHet = SanPham::with('danhMuc')
            ->whereBetween('soLuong', [1, 5])
            ->orderBy('soLuong')
            ->limit(5)
            ->get();

        $coCauDon = $donHangStats->pluck('cnt', 'trangThai')->all();

        return view('staff.dashboard', compact(
            'tongSanPham', 'sanPhamConHang', 'tongDonHang', 'donChoXacNhan', 'donHangMoiNhat',
            'donCanXuLy', 'spSapHet', 'coCauDon'
        ) + ['doanhThu14Ngay' => $this->doanhThu14Ngay()]);
    }
}
