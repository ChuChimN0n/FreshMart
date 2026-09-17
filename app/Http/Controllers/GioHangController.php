<?php

namespace App\Http\Controllers;

use App\Models\ChiTietGioHang;
use App\Models\GioHang;
use App\Models\SanPham;
use App\Models\TaiKhoan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GioHangController extends Controller
{
    public function index(): View
    {
        $gioHang = Auth::user()->gioHang()->with('chiTietGioHangs.sanPham.danhMuc')->first();
        $pricesChanged = $gioHang?->applyCurrentPrices() ?? false;

        return view('giohang.index', compact('gioHang', 'pricesChanged'));
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'maSP' => 'required|integer|exists:SanPham,maSP',
            'soLuong' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($data): RedirectResponse {
            $user = TaiKhoan::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            $gioHang = $user->gioHang()->firstOrCreate([], ['tongTien' => 0]);
            $gioHang = GioHang::whereKey($gioHang->maGioHang)->lockForUpdate()->firstOrFail();
            $sanPham = SanPham::whereKey($data['maSP'])->lockForUpdate()->firstOrFail();
            $chiTiet = $gioHang->chiTietGioHangs()->where('maSP', $sanPham->maSP)->lockForUpdate()->first();
            $quantity = ($chiTiet?->soLuong ?? 0) + $data['soLuong'];

            if ($sanPham->trangThai !== SanPham::DANG_BAN || $quantity > $sanPham->soLuong) {
                return back()->with('error', 'Sản phẩm không còn bán hoặc vượt quá tồn kho!');
            }

            $gioHang->chiTietGioHangs()->updateOrCreate(['maSP' => $sanPham->maSP], [
                'soLuong' => $quantity,
                'donGia' => $sanPham->giaBan,
                'thanhTien' => $quantity * $sanPham->giaBan,
            ]);
            $gioHang->tinhTongTien();

            return back()->with('success', 'Đã thêm vào giỏ hàng!');
        }, 3);
    }

    public function update(Request $request, ChiTietGioHang $chitiet): RedirectResponse
    {
        $data = $request->validate(['soLuong' => 'required|integer|min:1']);

        return DB::transaction(function () use ($data, $chitiet): RedirectResponse {
            TaiKhoan::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            $gioHang = GioHang::whereKey($chitiet->maGioHang)->lockForUpdate()->firstOrFail();
            abort_unless($gioHang->maTK === Auth::id(), 403);
            $detail = $gioHang->chiTietGioHangs()->whereKey($chitiet->maCTGH)->lockForUpdate()->firstOrFail();
            $sanPham = SanPham::whereKey($detail->maSP)->lockForUpdate()->firstOrFail();

            if ($sanPham->trangThai !== SanPham::DANG_BAN || $data['soLuong'] > $sanPham->soLuong) {
                return back()->with('error', 'Sản phẩm không còn bán hoặc vượt quá tồn kho!');
            }

            $detail->update([
                'soLuong' => $data['soLuong'],
                'donGia' => $sanPham->giaBan,
                'thanhTien' => $data['soLuong'] * $sanPham->giaBan,
            ]);
            $gioHang->tinhTongTien();

            return back()->with('success', 'Cập nhật giỏ hàng thành công!');
        }, 3);
    }

    public function remove(ChiTietGioHang $chitiet): RedirectResponse
    {
        return DB::transaction(function () use ($chitiet): RedirectResponse {
            TaiKhoan::whereKey(Auth::id())->lockForUpdate()->firstOrFail();
            $gioHang = GioHang::whereKey($chitiet->maGioHang)->lockForUpdate()->firstOrFail();
            abort_unless($gioHang->maTK === Auth::id(), 403);
            $gioHang->chiTietGioHangs()->whereKey($chitiet->maCTGH)->delete();
            $gioHang->tinhTongTien();

            return back()->with('success', 'Đã xóa khỏi giỏ hàng!');
        }, 3);
    }
}
