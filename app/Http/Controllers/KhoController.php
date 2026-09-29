<?php

namespace App\Http\Controllers;

use App\Models\LichSuKho;
use App\Models\SanPham;
use Illuminate\Http\Request;

class KhoController extends Controller
{
    const LOAI_LABEL = [
        LichSuKho::NHAP_HANG => 'Nhập hàng',
        LichSuKho::BAN_HANG => 'Bán hàng',
        LichSuKho::HOAN_DON => 'Hoàn kho',
    ];

    const LOAI_BADGE = [
        LichSuKho::NHAP_HANG => 'bg-green-100 text-green-700',
        LichSuKho::BAN_HANG => 'bg-blue-100 text-blue-700',
        LichSuKho::HOAN_DON => 'bg-amber-100 text-amber-700',
    ];

    public function index(Request $request)
    {
        $query = SanPham::with('danhMuc');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search): void {
                $q->where('tenSP', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%");
            });
        }
        $sanPhams = $query->orderBy('tenSP')->paginate(10);

        $thongKe = [
            'tong' => SanPham::count(),
            'sapHet' => SanPham::where('soLuong', '>', 0)->whereColumn('soLuong', '<=', 'mucTonToiThieu')->count(),
            'hetHang' => SanPham::where('soLuong', '<=', 0)->count(),
        ];

        return view('staff.kho.index', compact('sanPhams', 'thongKe'));
    }

    public function history(Request $request)
    {
        $query = LichSuKho::with('sanPham');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search): void {
                $q->whereHas('sanPham', fn ($sp) => $sp->where('tenSP', 'like', "%$search%")->orWhere('sku', 'like', "%$search%"));
            });
        }
        if ($request->filled('loaiBienDong')
            && in_array($request->loaiBienDong, [LichSuKho::NHAP_HANG, LichSuKho::BAN_HANG, LichSuKho::HOAN_DON], true)) {
            $query->where('loaiBienDong', $request->loaiBienDong);
        }
        if ($request->filled('tuNgay')) {
            $query->whereDate('thoiGian', '>=', $request->tuNgay);
        }
        if ($request->filled('denNgay')) {
            $query->whereDate('thoiGian', '<=', $request->denNgay);
        }
        $lichSus = $query->orderByDesc('thoiGian')->paginate(15);

        return view('staff.kho.history', [
            'lichSus' => $lichSus,
            'loaiLabels' => self::LOAI_LABEL,
            'loaiBadges' => self::LOAI_BADGE,
        ]);
    }

    public function alerts(Request $request)
    {
        $query = SanPham::with('danhMuc')
            ->where(function ($q): void {
                $q->where('soLuong', '<=', 0)
                    ->orWhereColumn('soLuong', '<=', 'mucTonToiThieu');
            });
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search): void {
                $q->where('tenSP', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%");
            });
        }
        if ($request->trangThaiTon === 'het') {
            $query->where('soLuong', '<=', 0);
        } elseif ($request->trangThaiTon === 'saphet') {
            $query->where('soLuong', '>', 0)->whereColumn('soLuong', '<=', 'mucTonToiThieu');
        }
        $sanPhams = $query->orderBy('soLuong')->paginate(10);

        $thongKe = [
            'hetHang' => SanPham::where('soLuong', '<=', 0)->count(),
            'sapHet' => SanPham::where('soLuong', '>', 0)->whereColumn('soLuong', '<=', 'mucTonToiThieu')->count(),
        ];

        return view('staff.kho.alerts', compact('sanPhams', 'thongKe'));
    }
}
