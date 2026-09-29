<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\GioHang;
use App\Models\NhaCungCap;
use App\Models\NhaCungCapSanPham;
use App\Models\SanPham;
use App\Services\CodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SanPhamController extends Controller
{
    public function index(Request $request)
    {
        $query = SanPham::with(['nhaCungCap', 'danhMuc']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tenSP', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%");
                if (ctype_digit($search)) {
                    $q->orWhere('maSP', (int) $search);
                }
            });
        }
        if ($request->filled('maDM')) {
            $query->where('maDM', $request->maDM);
        }
        if ($request->filled('maNCC')) {
            $query->where('maNCC', $request->maNCC);
        }
        if ($request->filled('trangThai')
            && in_array($request->trangThai, [SanPham::DANG_BAN, SanPham::NGUNG_BAN], true)) {
            $query->where('trangThai', $request->trangThai);
        }
        if ($request->filled('tonKho')) {
            match ($request->tonKho) {
                'het' => $query->where('soLuong', '<=', 0),
                'saphet' => $query->where('soLuong', '>', 0)->whereColumn('soLuong', '<=', 'mucTonToiThieu'),
                'con' => $query->where('soLuong', '>', 0),
                default => null,
            };
        }
        match ($request->input('sort')) {
            'ten_asc' => $query->orderBy('tenSP')->orderBy('maSP', 'desc'),
            'gia_asc' => $query->orderBy('giaBan')->orderBy('maSP', 'desc'),
            'gia_desc' => $query->orderByDesc('giaBan')->orderBy('maSP', 'desc'),
            default => $query->orderBy('maSP', 'desc'),
        };
        $sanPhams = $query->paginate(10);
        $danhMucs = DanhMuc::ordered()->get();
        $nhaCungCaps = NhaCungCap::orderBy('tenNCC')->get();

        $thongKe = [
            'tong' => SanPham::count(),
            'dangBan' => SanPham::where('trangThai', SanPham::DANG_BAN)->count(),
            'sapHet' => SanPham::where('soLuong', '>', 0)->whereColumn('soLuong', '<=', 'mucTonToiThieu')->count(),
            'hetHang' => SanPham::where('soLuong', '<=', 0)->count(),
        ];

        return view('staff.sanpham.index', compact('sanPhams', 'danhMucs', 'nhaCungCaps', 'thongKe'));
    }

    public function create()
    {
        $nhaCungCaps = NhaCungCap::orderBy('tenNCC')->get();
        $danhMucs = DanhMuc::ordered()->get();

        return view('staff.sanpham.create', compact('nhaCungCaps', 'danhMucs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'maNCC' => 'required|exists:NhaCungCap,maNCC',
            'maDM' => 'required|exists:DanhMuc,maDM',
            'tenSP' => 'required|string|max:150|unique:SanPham,tenSP',
            'hinhAnh' => 'nullable|image|max:2048',
            'donVi' => 'required|string|in:kg,quả,bó,gói,chai,hộp,thùng,bịch,cây,củ,cái',
            'giaBan' => 'required|numeric|min:0',
            'moTa' => 'nullable|string',
            'mucTonToiThieu' => 'required|integer|min:0',
            'trangThai' => ['required', Rule::in([SanPham::DANG_BAN, SanPham::NGUNG_BAN])],
        ], [
            'tenSP.unique' => 'Sản phẩm đã được sử dụng',
        ]);

        // SKU luôn do hệ thống sinh, bỏ qua mọi giá trị client gửi lên.
        // insertUnique tự sinh lại mã và retry khi đua trùng unique.
        // soLuong ban đầu luôn 0 (bỏ qua mọi giá trị client gửi lên):
        // tồn kho chỉ tăng qua xác nhận phiếu nhập.
        $data = $request->only('maNCC', 'maDM', 'tenSP', 'donVi', 'giaBan', 'moTa', 'mucTonToiThieu', 'trangThai')
            + ['soLuong' => 0];

        $newPath = null;
        try {
            if ($request->hasFile('hinhAnh')) {
                $newPath = $request->file('hinhAnh')->store('sanpham', 'public');
                if (! $newPath) {
                    throw new \RuntimeException('Không lưu được ảnh sản phẩm.');
                }
                $data['hinhAnh'] = $newPath;
            }
            $sanPham = CodeGenerator::insertUnique(
                fn () => SanPham::create($data + ['sku' => CodeGenerator::next('sanpham', ['maDM' => $data['maDM']])]),
                'sku'
            );

            // Đồng bộ quan hệ NCC - SP để phiếu nhập thấy được sản phẩm mới.
            NhaCungCapSanPham::firstOrCreate(
                ['maNCC' => $sanPham->maNCC, 'maSP' => $sanPham->maSP],
                ['trangThai' => NhaCungCapSanPham::HOAT_DONG]
            );
        } catch (\Throwable $e) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            report($e);

            return back()->withInput()->with('error', 'Chưa thể lưu sản phẩm. Vui lòng thử lại!');
        }

        return redirect()->route('staff.sanpham.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit(SanPham $sanpham)
    {
        $nhaCungCaps = NhaCungCap::orderBy('tenNCC')->get();
        $danhMucs = DanhMuc::ordered()->get();

        return view('staff.sanpham.edit', ['sanPham' => $sanpham, 'nhaCungCaps' => $nhaCungCaps, 'danhMucs' => $danhMucs]);
    }

    public function update(Request $request, SanPham $sanpham): RedirectResponse
    {
        $request->validate([
            'maNCC' => 'required|exists:NhaCungCap,maNCC',
            'maDM' => 'required|exists:DanhMuc,maDM',
            'tenSP' => 'required|string|max:150',
            'hinhAnh' => 'nullable|image|max:2048',
            'donVi' => 'required|string|in:kg,quả,bó,gói,chai,hộp,thùng,bịch,cây,củ,cái',
            'giaBan' => 'required|numeric|min:0',
            'moTa' => 'nullable|string',
            'mucTonToiThieu' => 'required|integer|min:0',
            'trangThai' => ['required', Rule::in([SanPham::DANG_BAN, SanPham::NGUNG_BAN])],
        ]);

        // SKU đã khóa: không cho đổi qua form sửa, bỏ qua mọi giá trị client gửi lên.
        // soLuong cũng khóa: tồn chỉ đổi qua nhập/bán/hoàn kho.
        $data = $request->only('maNCC', 'maDM', 'tenSP', 'donVi', 'giaBan', 'moTa', 'mucTonToiThieu', 'trangThai');

        $newPath = null;
        try {
            if ($request->hasFile('hinhAnh')) {
                $newPath = $request->file('hinhAnh')->store('sanpham', 'public');
                if (! $newPath) {
                    throw new \RuntimeException('Không lưu được ảnh sản phẩm.');
                }
                $data['hinhAnh'] = $newPath;
            }

            $oldPath = DB::transaction(function () use ($sanpham, $data): ?string {
                $product = SanPham::whereKey($sanpham->maSP)->lockForUpdate()->firstOrFail();
                $oldPath = $product->hinhAnh;
                $oldNCC = $product->maNCC;
                $product->update($data);

                // Đổi NCC: ghi thêm cặp mới, giữ cặp cũ cho lịch sử phiếu nhập.
                if ((int) $data['maNCC'] !== (int) $oldNCC) {
                    NhaCungCapSanPham::firstOrCreate(
                        ['maNCC' => (int) $data['maNCC'], 'maSP' => $product->maSP],
                        ['trangThai' => NhaCungCapSanPham::HOAT_DONG]
                    );
                }

                return $oldPath;
            }, 3);
        } catch (\Throwable $e) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            report($e);

            return back()->withInput()->with('error', 'Chưa thể cập nhật sản phẩm. Ảnh cũ vẫn được giữ lại.');
        }

        if ($newPath && $oldPath) {
            Storage::disk('public')->delete(str_starts_with($oldPath, 'sanpham/') ? $oldPath : 'sanpham/'.$oldPath);
        }

        return redirect()->route('staff.sanpham.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    public function destroy(SanPham $sanpham): RedirectResponse
    {
        if ($sanpham->chiTietDonHangs()->exists()) {
            return back()->with('error', 'Không thể xóa sản phẩm vì đã có trong đơn hàng!');
        }

        if ($sanpham->chiTietPhieuNhaps()->exists()) {
            return back()->with('error', 'Không thể xóa sản phẩm vì đã phát sinh nhập hàng!');
        }

        $imagePath = $sanpham->hinhAnh;
        $maGioHangs = $sanpham->chiTietGioHangs()->pluck('maGioHang')->unique()->all();

        try {
            DB::transaction(function () use ($sanpham): void {
                $sanpham->chiTietGioHangs()->delete();
                $sanpham->danhGias()->delete();
                NhaCungCapSanPham::where('maSP', $sanpham->maSP)->delete();
                $sanpham->delete();
            });
        } catch (QueryException $e) {
            if ($sanpham->chiTietDonHangs()->exists()) {
                return back()->with('error', 'Không thể xóa sản phẩm vì đã có trong đơn hàng!');
            }

            if ($sanpham->chiTietPhieuNhaps()->exists() || $sanpham->lichSuKhos()->exists()) {
                return back()->with('error', 'Không thể xóa sản phẩm vì đã phát sinh nhập/bán hàng!');
            }

            throw $e;
        }

        foreach ($maGioHangs as $maGioHang) {
            GioHang::whereKey($maGioHang)->first()?->tinhTongTien();
        }

        if ($imagePath) {
            Storage::disk('public')->delete(str_starts_with($imagePath, 'sanpham/') ? $imagePath : 'sanpham/'.$imagePath);
        }

        return back()->with('success', 'Xóa sản phẩm thành công!');
    }
}
