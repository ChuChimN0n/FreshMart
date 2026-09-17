<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDonHang;
use App\Models\DanhMuc;
use App\Models\DonHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = SanPham::with(['danhMuc', 'nhaCungCap'])
            ->where('trangThai', SanPham::DANG_BAN)
            ->where('soLuong', '>', 0);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('tenSP', 'like', "%$search%");
        }
        if ($request->filled('maDM')) {
            $query->where('maDM', $request->maDM);
        }

        $sanPhams = $query->orderBy('maSP', 'desc')->paginate(12);
        $danhMucs = collect(Cache::flexible('danhMucs', [60, 120], fn () => DanhMuc::ordered()
            ->get(['maDM', 'tenDM'])
            ->map(fn ($dm) => ['maDM' => $dm->maDM, 'tenDM' => $dm->tenDM])
            ->values()
            ->all()))
            ->map(fn ($dm) => (object) $dm);

        return view('home.index', compact('sanPhams', 'danhMucs'));
    }

    public function show(SanPham $sanpham)
    {
        $sanpham->load(['danhMuc', 'nhaCungCap']);

        $danhGias = $sanpham->danhGias()->with('taiKhoan')->orderByDesc('maDanhGia')->paginate(10, ['*'], 'reviews_page');
        $starCounts = $sanpham->danhGias()->selectRaw('soSao, COUNT(*) AS total')->groupBy('soSao')->pluck('total', 'soSao');
        $tongDanhGia = $starCounts->sum();
        $trungBinhSao = $tongDanhGia > 0 ? round($starCounts->map(fn ($count, $stars) => $count * $stars)->sum() / $tongDanhGia, 1) : 0;

        $soLuongDaBan = ChiTietDonHang::where('maSP', $sanpham->maSP)
            ->whereHas('donHang', function ($q) {
                $q->where('trangThai', '!=', DonHang::DA_HUY);
            })
            ->sum('soLuong');

        $phanTramSao = collect(range(5, 1))->map(function ($sao) use ($starCounts, $tongDanhGia) {
            $soLuong = $starCounts->get($sao, 0);

            return [
                'soSao' => $sao,
                'soLuong' => $soLuong,
                'phanTram' => $tongDanhGia > 0 ? round($soLuong / $tongDanhGia * 100) : 0,
            ];
        })->all();

        $user = Auth::user();
        $daMua = false;
        if ($user) {
            $daMua = DonHang::where('maTK', $user->maTK)
                ->where('trangThai', DonHang::HOAN_THANH)
                ->whereHas('chiTietDonHangs', function ($q) use ($sanpham) {
                    $q->where('maSP', $sanpham->maSP);
                })
                ->exists();
        }

        $sanPhamLienQuan = SanPham::with('danhMuc')
            ->where('maDM', $sanpham->maDM)
            ->where('maSP', '!=', $sanpham->maSP)
            ->where('trangThai', SanPham::DANG_BAN)
            ->where('soLuong', '>', 0)
            ->limit(4)
            ->get();

        if ($sanPhamLienQuan->count() < 4) {
            $boSung = SanPham::with('danhMuc')
                ->where('maDM', '!=', $sanpham->maDM)
                ->where('trangThai', SanPham::DANG_BAN)
                ->where('soLuong', '>', 0)
                ->whereNotIn('maSP', $sanPhamLienQuan->pluck('maSP'))
                ->limit(4 - $sanPhamLienQuan->count())
                ->get();
            $sanPhamLienQuan = $sanPhamLienQuan->concat($boSung);
        }

        return view('home.show', compact(
            'sanpham',
            'danhGias',
            'trungBinhSao',
            'tongDanhGia',
            'soLuongDaBan',
            'phanTramSao',
            'daMua',
            'sanPhamLienQuan'
        ));
    }
}
