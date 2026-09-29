<?php

namespace App\Http\Controllers;

use App\Models\NhaCungCap;
use App\Models\NhaCungCapSanPham;
use App\Models\PhieuNhap;
use App\Models\SanPham;
use App\Services\CodeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhieuNhapController extends Controller
{
    public function index(Request $request)
    {
        $query = PhieuNhap::with(['nhaCungCap', 'nguoiTao']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search): void {
                $q->where('maPhieu', 'like', "%$search%")
                    ->orWhereHas('nhaCungCap', fn ($ncc) => $ncc->where('tenNCC', 'like', "%$search%"));
            });
        }
        if ($request->filled('trangThai')
            && in_array($request->trangThai, [PhieuNhap::NHAP, PhieuNhap::DA_XAC_NHAN], true)) {
            $query->where('trangThai', $request->trangThai);
        }
        if ($request->filled('tuNgay')) {
            $query->whereDate('ngayTao', '>=', $request->tuNgay);
        }
        if ($request->filled('denNgay')) {
            $query->whereDate('ngayTao', '<=', $request->denNgay);
        }
        $phieuNhaps = $query->orderByDesc('ngayTao')->paginate(10);

        $thongKe = [
            'tong' => PhieuNhap::count(),
            'choXacNhan' => PhieuNhap::where('trangThai', PhieuNhap::NHAP)->count(),
            'daXacNhan' => PhieuNhap::where('trangThai', PhieuNhap::DA_XAC_NHAN)->count(),
        ];

        return view('staff.nhaphang.index', compact('phieuNhaps', 'thongKe'));
    }

    public function create(Request $request)
    {
        $nhaCungCaps = NhaCungCap::orderBy('tenNCC')->get();
        $maNCC = (int) old('maNCC', $request->integer('maNCC'));

        // Preload toàn bộ SP đang bán + map NCC -> SP để lọc client-side (không reload mất dữ liệu).
        $sanPhams = SanPham::where('trangThai', SanPham::DANG_BAN)->orderBy('tenSP')->get();
        $sanPhamById = $sanPhams->mapWithKeys(fn (SanPham $sp): array => [$sp->maSP => [
            'maSP' => $sp->maSP,
            'tenSP' => $sp->tenSP,
            'donVi' => $sp->donVi,
            'ton' => $sp->soLuong,
        ]])->all();
        $nccSanPhamMap = NhaCungCapSanPham::where('trangThai', NhaCungCapSanPham::HOAT_DONG)
            ->whereIn('maSP', array_keys($sanPhamById))
            ->get()
            ->groupBy('maNCC')
            ->map(fn ($rows): array => $rows->pluck('maSP')->all())
            ->all();

        return view('staff.nhaphang.create', compact('nhaCungCaps', 'sanPhams', 'sanPhamById', 'nccSanPhamMap', 'maNCC'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'maNCC' => 'required|exists:NhaCungCap,maNCC',
            'ghiChu' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.maSP' => 'required|integer|exists:SanPham,maSP',
            'items.*.soLuong' => 'required|integer|min:1',
            'items.*.giaNhap' => 'required|numeric|min:0',
        ], [
            'items.required' => 'Phiếu nhập phải có ít nhất một sản phẩm.',
            'items.min' => 'Phiếu nhập phải có ít nhất một sản phẩm.',
        ]);

        $maNCC = (int) $request->maNCC;

        try {
            $phieu = DB::transaction(function () use ($request, $maNCC): PhieuNhap {
                $tongTien = 0;
                $chiTietData = [];

                foreach ($request->items as $item) {
                    $sanPham = SanPham::whereKey((int) $item['maSP'])->first();

                    if (! $sanPham || $sanPham->trangThai !== SanPham::DANG_BAN) {
                        throw new \DomainException('Sản phẩm '.($sanPham->tenSP ?? '').' hiện không nhập được!');
                    }

                    // Sản phẩm phải thuộc danh sách NCC này cung cấp.
                    $duocCungCap = NhaCungCapSanPham::where('maNCC', $maNCC)
                        ->where('maSP', $sanPham->maSP)
                        ->where('trangThai', NhaCungCapSanPham::HOAT_DONG)
                        ->exists();
                    if (! $duocCungCap) {
                        throw new \DomainException("Sản phẩm {$sanPham->tenSP} không thuộc nhà cung cấp đã chọn!");
                    }

                    $soLuong = (int) $item['soLuong'];
                    $giaNhap = (float) $item['giaNhap'];
                    $thanhTien = $soLuong * $giaNhap;
                    $tongTien += $thanhTien;

                    $chiTietData[] = [
                        'maSP' => $sanPham->maSP,
                        'soLuong' => $soLuong,
                        'giaNhap' => $giaNhap,
                        'thanhTien' => $thanhTien,
                    ];
                }

                // maPhieu do hệ thống sinh, retry khi đua trùng unique.
                $phieuNhap = CodeGenerator::insertUnique(
                    fn () => PhieuNhap::create([
                        'maPhieu' => CodeGenerator::next('phieunhap'),
                        'maNCC' => $maNCC,
                        'maNguoiTao' => Auth::id(),
                        'ngayTao' => now(),
                        'tongTien' => $tongTien,
                        'trangThai' => PhieuNhap::NHAP,
                        'ghiChu' => $request->ghiChu,
                    ]),
                    'maPhieu'
                );

                foreach ($chiTietData as $ct) {
                    $ct['maPN'] = $phieuNhap->maPN;
                    $phieuNhap->chiTiets()->create($ct);
                }

                return $phieuNhap;
            }, 3);
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Chưa thể tạo phiếu nhập. Vui lòng thử lại!');
        }

        return redirect()->route('staff.nhaphang.show', $phieu)->with('success', 'Tạo phiếu nhập thành công!');
    }

    public function show(PhieuNhap $phieunhap)
    {
        $phieunhap->load(['chiTiets.sanPham', 'nhaCungCap', 'nguoiTao']);

        return view('staff.nhaphang.show', ['phieuNhap' => $phieunhap]);
    }

    public function confirm(PhieuNhap $phieunhap): RedirectResponse
    {
        try {
            $ok = $phieunhap->xacNhan();
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Chưa thể xác nhận phiếu nhập. Vui lòng thử lại!');
        }

        if (! $ok) {
            return back()->with('error', 'Phiếu nhập đã được xác nhận trước đó!');
        }

        return back()->with('success', 'Xác nhận nhập kho thành công!');
    }
}
