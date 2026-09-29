<?php

namespace App\Http\Controllers;

use App\Models\ChiTietDonHang;
use App\Models\DanhGia;
use App\Models\DonHang;
use App\Models\LichSuKho;
use App\Models\SanPham;
use App\Models\TaiKhoan;
use App\Services\CodeGenerator;
use Illuminate\Http\RedirectResponse;
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

        $pricesChanged = $gioHang->applyCurrentPrices();

        return view('donhang.checkout', compact('gioHang', 'pricesChanged'));
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $request->validate([
            'tenNguoiNhan' => 'required|string|max:100',
            'soDienThoai' => 'required|string|max:15',
            'diaChi' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        try {
            DB::transaction(function () use ($user, $request): void {
                $account = TaiKhoan::whereKey($user->maTK)->lockForUpdate()->firstOrFail();
                if (! $account->isActive()) {
                    throw new \DomainException('Tài khoản đã bị khóa!');
                }

                $gioHang = $account->gioHang()->lockForUpdate()->first();
                $details = $gioHang?->chiTietGioHangs()->orderBy('maSP')->lockForUpdate()->get();
                if (! $details || $details->isEmpty()) {
                    throw new \DomainException('Giỏ hàng trống hoặc đã được đặt hàng!');
                }

                $tongTien = 0;
                $chiTietData = [];

                foreach ($details as $ct) {
                    $sanPham = SanPham::whereKey($ct->maSP)->lockForUpdate()->first();

                    if (! $sanPham || $sanPham->trangThai !== SanPham::DANG_BAN || $ct->soLuong < 1 || $sanPham->soLuong < $ct->soLuong) {
                        $ten = $sanPham->tenSP ?? 'Sản phẩm';
                        throw new \DomainException("Sản phẩm {$ten} hiện không còn bán hoặc không đủ số lượng!");
                    }

                    $thanhtien = $ct->soLuong * $sanPham->giaBan;
                    $tongTien += $thanhtien;

                    $chiTietData[] = [
                        'maSP' => $ct->maSP,
                        'soLuong' => $ct->soLuong,
                        'donGia' => $sanPham->giaBan,
                        'thanhTien' => $thanhtien,
                        'tonTruoc' => $sanPham->soLuong,
                    ];

                    // Cap nhat ton kho (lich su BAN_HANG ghi sau khi co maDH)
                    $sanPham->decrement('soLuong', $ct->soLuong);
                }

                // Tao DonHang (maDon sinh trong transaction, retry sẵn có lo vụ trùng)
                $donHang = DonHang::create([
                    'maTK' => $user->maTK,
                    'maDon' => CodeGenerator::next('donhang'),
                    'ngayDat' => now(),
                    'tenNguoiNhan' => $request->tenNguoiNhan,
                    'soDienThoai' => $request->soDienThoai,
                    'diaChi' => $request->diaChi,
                    'tongTien' => $tongTien,
                    'trangThai' => DonHang::CHO_XAC_NHAN,
                ]);

                // Tao ChiTietDonHang + ghi lich su BAN_HANG
                foreach ($chiTietData as $ct) {
                    $tonTruoc = $ct['tonTruoc'];
                    unset($ct['tonTruoc']);
                    $ct['maDH'] = $donHang->maDH;
                    ChiTietDonHang::create($ct);
                    LichSuKho::ghiNhan(
                        maSP: $ct['maSP'],
                        loaiBienDong: LichSuKho::BAN_HANG,
                        soLuong: $ct['soLuong'],
                        tonTruoc: $tonTruoc,
                        tonSau: $tonTruoc - $ct['soLuong'],
                        maDH: $donHang->maDH,
                        maTK: $user->maTK,
                    );
                }

                // Xoa gio hang
                $gioHang->chiTietGioHangs()->delete();
                $gioHang->update(['tongTien' => 0]);
            }, 3);
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Chưa thể đặt hàng. Vui lòng thử lại sau!');
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

    public function cancel(DonHang $donhang): RedirectResponse
    {
        if ($donhang->maTK !== Auth::id()) {
            abort(403);
        }

        if (! $donhang->transitionTo(DonHang::DA_HUY, customerCancellation: true)) {
            if ($donhang->trangThai === DonHang::DANG_GIAO) {
                return back()->with('error', 'Đơn hàng đang giao, không thể hủy!');
            }

            return back()->with('error', 'Đơn hàng không thể hủy!');
        }

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
                    ->orWhere('soDienThoai', 'like', "%$search%")
                    ->orWhere('maDon', 'like', "%$search%");
            });
        }
        if ($request->filled('trangThai')) {
            $query->where('trangThai', $request->trangThai);
        }
        $donHangs = $query->orderByDesc('ngayDat')->paginate(10);

        $thongKe = DonHang::select('trangThai', DB::raw('COUNT(*) AS cnt'))
            ->groupBy('trangThai')
            ->pluck('cnt', 'trangThai')
            ->all();

        return view('nhanvien.donhang.index', compact('donHangs', 'thongKe'));
    }

    public function staffDetail(DonHang $donhang)
    {
        $donhang->load(['chiTietDonHangs.sanPham', 'taiKhoan']);

        return view('nhanvien.donhang.detail', ['donHang' => $donhang]);
    }

    public function updateStatus(Request $request, DonHang $donhang): RedirectResponse
    {
        $request->validate([
            'trangThai' => ['required', Rule::in([
                DonHang::DA_XAC_NHAN,
                DonHang::DANG_GIAO,
                DonHang::HOAN_THANH,
                DonHang::DA_HUY,
            ])],
        ]);

        if (! $donhang->transitionTo($request->string('trangThai')->toString())) {
            return back()->with('error', 'Không thể chuyển trạng thái này!');
        }

        return back()->with('success', 'Cập nhật trạng thái thành công!');
    }
}
