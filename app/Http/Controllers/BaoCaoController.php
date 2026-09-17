<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BaoCaoController extends Controller
{
    public function index()
    {
        return view('admin.baocao.index');
    }

    public function sanPham(Request $request)
    {
        $request->validate(
            [
                'tuNgay' => 'required|date',
                'denNgay' => 'required|date|after_or_equal:tuNgay',
            ],
            [
                'tuNgay.required' => 'Vui lòng chọn ngày bắt đầu.',
                'tuNgay.date' => 'Ngày bắt đầu không đúng định dạng.',
                'denNgay.required' => 'Vui lòng chọn ngày kết thúc.',
                'denNgay.date' => 'Ngày kết thúc không đúng định dạng.',
                'denNgay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu. Vui lòng chọn lại ngày.',
            ],
            [
                'tuNgay' => 'ngày bắt đầu',
                'denNgay' => 'ngày kết thúc',
            ]
        );

        $tuNgay = $request->tuNgay;
        $denNgay = $request->denNgay;

        $data = ChiTietDonHang::select(
            'SanPham.maSP',
            'SanPham.tenSP',
            'SanPham.donVi',
            'SanPham.giaBan',
            DB::raw('SUM(ChiTietDonHang.soLuong) AS tongBan'),
            DB::raw('SUM(ChiTietDonHang.thanhTien) AS tongTien')
        )
            ->join('DonHang', 'ChiTietDonHang.maDH', '=', 'DonHang.maDH')
            ->join('SanPham', 'ChiTietDonHang.maSP', '=', 'SanPham.maSP')
            ->whereIn('DonHang.trangThai', [DonHang::DA_XAC_NHAN, DonHang::DANG_GIAO, DonHang::HOAN_THANH])
            ->whereDate('DonHang.ngayDat', '>=', $tuNgay)
            ->whereDate('DonHang.ngayDat', '<=', $denNgay)
            ->groupBy('SanPham.maSP', 'SanPham.tenSP', 'SanPham.donVi', 'SanPham.giaBan')
            ->orderByDesc('tongBan')
            ->get();

        $tongBanTheoDonVi = $data->groupBy('donVi')->map(fn ($g) => $g->sum('tongBan'));
        $tongTien = $data->sum('tongTien');

        return view('admin.baocao.sanpham', compact('data', 'tuNgay', 'denNgay', 'tongBanTheoDonVi', 'tongTien'));
    }

    public function doanhThu(Request $request)
    {
        $request->validate(
            [
                'tuNgay' => 'required|date',
                'denNgay' => 'required|date|after_or_equal:tuNgay',
            ],
            [
                'tuNgay.required' => 'Vui lòng chọn ngày bắt đầu.',
                'tuNgay.date' => 'Ngày bắt đầu không đúng định dạng.',
                'denNgay.required' => 'Vui lòng chọn ngày kết thúc.',
                'denNgay.date' => 'Ngày kết thúc không đúng định dạng.',
                'denNgay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu. Vui lòng chọn lại ngày.',
            ],
            [
                'tuNgay' => 'ngày bắt đầu',
                'denNgay' => 'ngày kết thúc',
            ]
        );

        $tuNgay = $request->tuNgay;
        $denNgay = $request->denNgay;

        $data = DonHang::select(
            DB::raw('DATE(ngayDat) AS ngay'),
            DB::raw('COUNT(*) AS soDon'),
            DB::raw('SUM(tongTien) AS doanhThu')
        )
            ->whereIn('trangThai', [DonHang::DA_XAC_NHAN, DonHang::DANG_GIAO, DonHang::HOAN_THANH])
            ->whereDate('ngayDat', '>=', $tuNgay)
            ->whereDate('ngayDat', '<=', $denNgay)
            ->groupBy(DB::raw('DATE(ngayDat)'))
            ->orderBy('ngay')
            ->get();

        $tongDoanhThu = $data->sum('doanhThu');
        $tongSoDon = $data->sum('soDon');

        return view('admin.baocao.doanhthu', compact('data', 'tuNgay', 'denNgay', 'tongDoanhThu', 'tongSoDon'));
    }

    public function donHang(Request $request)
    {
        $request->validate(
            [
                'tuNgay' => 'required|date',
                'denNgay' => 'required|date|after_or_equal:tuNgay',
            ],
            [
                'tuNgay.required' => 'Vui lòng chọn ngày bắt đầu.',
                'tuNgay.date' => 'Ngày bắt đầu không đúng định dạng.',
                'denNgay.required' => 'Vui lòng chọn ngày kết thúc.',
                'denNgay.date' => 'Ngày kết thúc không đúng định dạng.',
                'denNgay.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu. Vui lòng chọn lại ngày.',
            ],
            [
                'tuNgay' => 'ngày bắt đầu',
                'denNgay' => 'ngày kết thúc',
            ]
        );

        $tuNgay = $request->tuNgay;
        $denNgay = $request->denNgay;

        $data = DonHang::select(
            'trangThai',
            DB::raw('COUNT(*) AS soLuong'),
            DB::raw('SUM(tongTien) AS tongTien')
        )
            ->whereDate('ngayDat', '>=', $tuNgay)
            ->whereDate('ngayDat', '<=', $denNgay)
            ->groupBy('trangThai')
            ->get()
            ->map(function ($item) {
                $item->label = DonHang::TRANG_THAI[$item->trangThai] ?? $item->trangThai;

                return $item;
            });

        $tongDon = $data->sum('soLuong');
        $tongTien = $data->sum('tongTien');

        return view('admin.baocao.donhang', compact('data', 'tuNgay', 'denNgay', 'tongDon', 'tongTien'));
    }
}
