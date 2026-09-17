<?php

namespace App\Http\Controllers;

use App\Models\DanhGia;
use App\Models\DonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DanhGiaController extends Controller
{
    // ========== KHACH HANG ==========
    public function store(Request $request)
    {
        $request->validate([
            'maSP' => 'required|exists:SanPham,maSP',
            'soSao' => 'required|integer|min:1|max:5',
            'noiDung' => 'required|string|max:500',
        ]);

        $user = Auth::user();

        // Kiem tra da mua san pham chua
        $daMua = DonHang::where('maTK', $user->maTK)
            ->where('trangThai', DonHang::HOAN_THANH)
            ->whereHas('chiTietDonHangs', function ($q) use ($request) {
                $q->where('maSP', $request->maSP);
            })
            ->exists();

        if (! $daMua) {
            return back()->with('error', 'Bạn chỉ có thể đánh giá sản phẩm đã mua!');
        }

        // Kiem tra da danh gia chua
        $daDanhGia = DanhGia::where('maTK', $user->maTK)
            ->where('maSP', $request->maSP)
            ->exists();

        if ($daDanhGia) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi!');
        }

        DanhGia::create([
            'maTK' => $user->maTK,
            'maSP' => $request->maSP,
            'soSao' => $request->soSao,
            'noiDung' => $request->noiDung,
        ]);

        return back()->with('success', 'Gửi đánh giá thành công!');
    }

    // ========== NHAN VIEN ==========
    public function staffIndex(Request $request)
    {
        $query = DanhGia::with(['taiKhoan', 'sanPham']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('sanPham', function ($q) use ($search) {
                $q->where('tenSP', 'like', "%$search%");
            });
        }
        $danhGias = $query->orderByDesc('maDanhGia')->paginate(10);

        return view('nhanvien.danhgia.index', compact('danhGias'));
    }
}
