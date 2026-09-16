<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\NhaCungCap;
use App\Models\SanPham;
use Illuminate\Http\Request;
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

    public function store(Request $request)
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

        if ($request->hasFile('hinhAnh')) {
            $data['hinhAnh'] = $request->file('hinhAnh')->store('sanpham', 'public');
        }

        SanPham::create($data);

        return redirect()->route('staff.sanpham.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    public function edit(SanPham $sanpham)
    {
        $nhaCungCaps = NhaCungCap::orderBy('tenNCC')->get();
        $danhMucs = DanhMuc::ordered()->get();

        return view('staff.sanpham.edit', ['sanPham' => $sanpham, 'nhaCungCaps' => $nhaCungCaps, 'danhMucs' => $danhMucs]);
    }

    public function update(Request $request, SanPham $sanpham)
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

        if ($request->hasFile('hinhAnh')) {
            if ($sanpham->hinhAnh) {
                $oldPath = $sanpham->hinhAnh;
                if (strpos($oldPath, 'sanpham/') !== 0) {
                    $oldPath = 'sanpham/'.$oldPath;
                }
                Storage::disk('public')->delete($oldPath);
            }
            $data['hinhAnh'] = $request->file('hinhAnh')->store('sanpham', 'public');
        }

        $sanpham->update($data);

        return redirect()->route('staff.sanpham.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }
}
