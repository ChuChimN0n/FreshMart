<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\GioHang;
use App\Models\NhaCungCap;
use App\Models\SanPham;
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
            $query->where('tenSP', 'like', "%$search%");
        }
        if ($request->filled('maDM')) {
            $query->where('maDM', $request->maDM);
        }
        $sanPhams = $query->orderBy('maSP', 'desc')->paginate(10);
        $danhMucs = DanhMuc::ordered()->get();

        return view('staff.sanpham.index', compact('sanPhams', 'danhMucs'));
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
            'soLuong' => 'required|integer|min:0',
            'trangThai' => ['required', Rule::in([SanPham::DANG_BAN, SanPham::NGUNG_BAN])],
        ],[
            'tenSP.unique'=> 'Sản phẩm đã được sử dụng',
        ]);

        $data = $request->only('maNCC', 'maDM', 'tenSP', 'donVi', 'giaBan', 'moTa', 'soLuong', 'trangThai');

        $newPath = null;
        try {
            if ($request->hasFile('hinhAnh')) {
                $newPath = $request->file('hinhAnh')->store('sanpham', 'public');
                if (! $newPath) {
                    throw new \RuntimeException('Không lưu được ảnh sản phẩm.');
                }
                $data['hinhAnh'] = $newPath;
            }
            SanPham::create($data);
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
            'soLuong' => 'required|integer|min:0',
            'trangThai' => ['required', Rule::in([SanPham::DANG_BAN, SanPham::NGUNG_BAN])],
        ]);

        $data = $request->only('maNCC', 'maDM', 'tenSP', 'donVi', 'giaBan', 'moTa', 'soLuong', 'trangThai');

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
                $product->update($data);

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

        $imagePath = $sanpham->hinhAnh;
        $maGioHangs = $sanpham->chiTietGioHangs()->pluck('maGioHang')->unique()->all();

        try {
            DB::transaction(function () use ($sanpham): void {
                $sanpham->chiTietGioHangs()->delete();
                $sanpham->danhGias()->delete();
                $sanpham->delete();
            });
        } catch (QueryException $e) {
            if ($sanpham->chiTietDonHangs()->exists()) {
                return back()->with('error', 'Không thể xóa sản phẩm vì đã có trong đơn hàng!');
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
