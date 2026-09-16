<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDonHang;
use App\Models\DanhGia;
use App\Models\DonHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DonHangController extends Controller
{
    // ========== KHACH HANG ==========
    public function checkout()
    {
        $gioHang = Auth::user()->gioHang()->with('chiTietGioHangs.sanPham')->first();
        if (! $gioHang || $gioHang->chiTietGioHangs->isEmpty()) {
            return redirect()->route('giohang.index')->with('error', 'Giỏ hàng trống!');
        }

        // Sync gia hien tai vao memory de hien thi chinh xac
        foreach ($gioHang->chiTietGioHangs as $ct) {
            if ($ct->sanPham) {
                $ct->donGia = $ct->sanPham->giaBan;
                $ct->thanhTien = $ct->soLuong * $ct->donGia;
            }
        }
        $gioHang->tongTien = $gioHang->chiTietGioHangs->sum('thanhTien');

        return view('donhang.checkout', compact('gioHang'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'tenNguoiNhan' => 'required|string|max:100',
            'soDienThoai' => 'required|string|max:15',
            'diaChi' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $gioHang = $user->gioHang()->with('chiTietGioHangs.sanPham')->first();

        if (! $gioHang || $gioHang->chiTietGioHangs->isEmpty()) {
            return redirect()->route('giohang.index')->with('error', 'Giỏ hàng trống!');
        }

        // Kiem tra ton kho + trang thai
        foreach ($gioHang->chiTietGioHangs as $ct) {
            if (! $ct->sanPham || $ct->sanPham->trangThai !== SanPham::DANG_BAN) {
                return back()->with('error', 'Sản phẩm trong giỏ hiện không còn bán!');
            }
            if ($ct->sanPham->soLuong < $ct->soLuong) {
                return back()->with('error', "Sản phẩm {$ct->sanPham->tenSP} chỉ còn {$ct->sanPham->soLuong}!");
            }
        }

        try {
            DB::transaction(function () use ($user, $gioHang, $request) {
                $tongTien = 0;
                $chiTietData = [];

                foreach ($gioHang->chiTietGioHangs as $ct) {
                    $sanPham = SanPham::whereKey($ct->maSP)->lockForUpdate()->first();

                    if (! $sanPham || $sanPham->trangThai !== SanPham::DANG_BAN || $sanPham->soLuong < $ct->soLuong) {
                        $ten = $ct->sanPham->tenSP ?? 'Sản phẩm';
                        throw new \RuntimeException("Sản phẩm {$ten} hiện không còn bán hoặc hết hàng!");
                    }

                    $thanhtien = $ct->soLuong * $sanPham->giaBan;
                    $tongTien += $thanhtien;

                    $chiTietData[] = [
                        'maSP' => $ct->maSP,
                        'soLuong' => $ct->soLuong,
                        'donGia' => $sanPham->giaBan,
                        'thanhTien' => $thanhtien,
                    ];

                    // Cap nhat ton kho
                    $sanPham->decrement('soLuong', $ct->soLuong);
                }

                // Tao DonHang
                $donHang = DonHang::create([
                    'maTK' => $user->maTK,
                    'ngayDat' => now(),
                    'tenNguoiNhan' => $request->tenNguoiNhan,
                    'soDienThoai' => $request->soDienThoai,
                    'diaChi' => $request->diaChi,
                    'tongTien' => $tongTien,
                    'trangThai' => DonHang::CHO_XAC_NHAN,
                ]);

                // Tao ChiTietDonHang
                foreach ($chiTietData as $ct) {
                    $ct['maDH'] = $donHang->maDH;
                    ChiTietDonHang::create($ct);
                }

                // Xoa gio hang
                $gioHang->chiTietGioHangs()->delete();
                $gioHang->update(['tongTien' => 0]);
            });
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('donhang.index')->with('success', 'Đặt hàng thành công!');
    }

    public function myOrders()
    {
        $donHangs = DonHang::where('maTK', Auth::id())
            ->orderByDesc('ngayDat')
            ->paginate(10);

        return view('donhang.index', compact('donHangs'));
    }

    public function myOrderDetail(DonHang $donhang)
    {
        if ($donhang->maTK !== Auth::id()) {
            abort(403);
        }
        $donhang->load('chiTietDonHangs.sanPham');

        $maSPs = $donhang->chiTietDonHangs->pluck('maSP')->filter()->values();
        $daDanhGia = DanhGia::where('maTK', Auth::id())
            ->whereIn('maSP', $maSPs)
            ->get();

        return view('donhang.detail', ['donHang' => $donhang, 'daDanhGia' => $daDanhGia]);
    }

    public function cancel(DonHang $donhang)
    {
        if ($donhang->maTK !== Auth::id()) {
            abort(403);
        }

        if (! $donhang->canCancel()) {
            return back()->with('error', 'Đơn hàng không thể hủy!');
        }

        DB::transaction(function () use ($donhang) {
            // Hoàn lại tồn kho
            $donhang->load('chiTietDonHangs.sanPham');
            foreach ($donhang->chiTietDonHangs as $ct) {
                if ($ct->sanPham) {
                    $ct->sanPham->increment('soLuong', $ct->soLuong);
                }
            }
            $donhang->update(['trangThai' => DonHang::DA_HUY]);
        });

        return back()->with('success', 'Đã hủy đơn hàng!');
    }

    // ========== NHAN VIEN ==========
    public function staffIndex(Request $request)
    {
        $query = DonHang::with('taiKhoan');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tenNguoiNhan', 'like', "%$search%")
                    ->orWhere('soDienThoai', 'like', "%$search%");
            });
        }
        if ($request->filled('trangThai')) {
            $query->where('trangThai', $request->trangThai);
        }
        $donHangs = $query->orderByDesc('ngayDat')->paginate(10);

        return view('nhanvien.donhang.index', compact('donHangs'));
    }

    public function staffDetail(DonHang $donhang)
    {
        $donhang->load(['chiTietDonHangs.sanPham', 'taiKhoan']);

        return view('nhanvien.donhang.detail', ['donHang' => $donhang]);
    }

    public function updateStatus(Request $request, DonHang $donhang)
    {
        $request->validate([
            'trangThai' => ['required', Rule::in([
                DonHang::DA_XAC_NHAN,
                DonHang::DANG_GIAO,
                DonHang::HOAN_THANH,
                DonHang::DA_HUY,
            ])],
        ]);

        $validTransitions = DonHang::VALID_TRANSITIONS;

        $current = $donhang->trangThai;
        $next = $request->trangThai;

        if (! isset($validTransitions[$current]) || ! in_array($next, $validTransitions[$current])) {
            return back()->with('error', 'Không thể chuyển trạng thái này!');
        }

        DB::transaction(function () use ($donhang, $next) {
            // Hoan ton kho neu huy
            if ($next === DonHang::DA_HUY) {
                $donhang->load('chiTietDonHangs.sanPham');
                foreach ($donhang->chiTietDonHangs as $ct) {
                    if ($ct->sanPham) {
                        $ct->sanPham->increment('soLuong', $ct->soLuong);
                    }
                }
            }
            $donhang->update(['trangThai' => $next]);
        });

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}
