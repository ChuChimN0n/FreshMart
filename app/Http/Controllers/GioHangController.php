<?php

namespace App\Http\Controllers;

use App\Models\ChiTietGioHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GioHangController extends Controller
{
    public function index()
    {
        $gioHang = Auth::user()->gioHang()->with('chiTietGioHangs.sanPham.danhMuc')->first();

        return view('giohang.index', compact('gioHang'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'maSP' => 'required|exists:SanPham,maSP',
            'soLuong' => 'required|integer|min:1',
        ]);

        $sanPham = SanPham::findOrFail($request->maSP);

        if ($sanPham->trangThai !== SanPham::DANG_BAN) {
            return back()->with('error', 'Sản phẩm này hiện không còn bán!');
        }

        if ($sanPham->soLuong < $request->soLuong) {
            return back()->with('error', 'Số lượng không đủ!');
        }

        $user = Auth::user();
        $gioHang = $user->gioHang()->firstOrCreate([], ['tongTien' => 0]);

        $chiTiet = ChiTietGioHang::where('maGioHang', $gioHang->maGioHang)
            ->where('maSP', $request->maSP)
            ->first();

        if ($chiTiet) {
            $soLuongMoi = $chiTiet->soLuong + $request->soLuong;
            if ($soLuongMoi > $sanPham->soLuong) {
                return back()->with('error', 'Vượt quá tồn kho!');
            }
            $chiTiet->update([
                'soLuong' => $soLuongMoi,
                'thanhTien' => $soLuongMoi * $chiTiet->donGia,
            ]);
        } else {
            ChiTietGioHang::create([
                'maGioHang' => $gioHang->maGioHang,
                'maSP' => $request->maSP,
                'soLuong' => $request->soLuong,
                'donGia' => $sanPham->giaBan,
                'thanhTien' => $request->soLuong * $sanPham->giaBan,
            ]);
        }

        $gioHang->tinhTongTien();

        return back()->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function update(Request $request, ChiTietGioHang $chitiet)
    {
        $request->validate([
            'soLuong' => 'required|integer|min:1',
        ]);

        if (! $chitiet->gioHang || $chitiet->gioHang->maTK !== Auth::id()) {
            abort(403);
        }

        $sanPham = $chitiet->sanPham;
        if ($request->soLuong > $sanPham->soLuong) {
            return back()->with('error', 'Vượt quá tồn kho!');
        }

        $chitiet->update([
            'soLuong' => $request->soLuong,
            'thanhTien' => $request->soLuong * $chitiet->donGia,
        ]);

        $chitiet->gioHang->tinhTongTien();

        return back()->with('success', 'Cập nhật giỏ hàng thành công!');
    }

    public function remove(ChiTietGioHang $chitiet)
    {
        if (! $chitiet->gioHang || $chitiet->gioHang->maTK !== Auth::id()) {
            abort(403);
        }

        $gioHang = $chitiet->gioHang;
        $chitiet->delete();
        $gioHang->tinhTongTien();

        return back()->with('success', 'Đã xóa khỏi giỏ hàng!');
    }
}
