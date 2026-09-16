<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class TaiKhoanController extends Controller
{
    public function index(Request $request)
    {
        $query = TaiKhoan::with('vaiTro');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('hoTen', 'like', "%$search%")
                    ->orWhere('tenDangNhap', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }
        if ($request->filled('maVT')) {
            $query->where('maVT', $request->maVT);
        }
        $taiKhoans = $query->orderBy('maTK', 'desc')->paginate(10);
        $vaiTros = VaiTro::orderBy('tenVT')->get();

        return view('admin.taikhoan.index', compact('taiKhoans', 'vaiTros'));
    }

    public function create()
    {
        $vaiTros = VaiTro::orderBy('tenVT')->get();

        return view('admin.taikhoan.create', compact('vaiTros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hoTen' => 'required|string|max:100',
            'tenDangNhap' => 'required|string|max:100|unique:TaiKhoan,tenDangNhap',
            'email' => 'required|email|max:100|unique:TaiKhoan,email',
            'soDienThoai' => 'required|string|max:15',
            'diaChi' => 'nullable|string|max:255',
            'maVT' => 'required|exists:VaiTro,maVT',
            'matKhau' => ['required', Password::min(6)],
            'trangThai' => 'required|in:HOAT_DONG,KHOA',
        ]);

        TaiKhoan::create([
            'hoTen' => $request->hoTen,
            'tenDangNhap' => $request->tenDangNhap,
            'email' => $request->email,
            'soDienThoai' => $request->soDienThoai,
            'diaChi' => $request->diaChi,
            'maVT' => $request->maVT,
            'matKhau' => $request->matKhau,
            'trangThai' => $request->trangThai,
        ]);

        return redirect()->route('admin.taikhoan.index')->with('success', 'Thêm tài khoản thành công!');
    }

    public function edit(TaiKhoan $taikhoan)
    {
        $vaiTros = VaiTro::orderBy('tenVT')->get();

        return view('admin.taikhoan.edit', ['taiKhoan' => $taikhoan, 'vaiTros' => $vaiTros]);
    }

    public function update(Request $request, TaiKhoan $taikhoan)
    {
        if ($taikhoan->isCustomer()) {
            return redirect()->route('admin.taikhoan.index')->with('error', 'Không thể sửa thông tin khách hàng!');
        }

        $request->validate([
            'hoTen' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:TaiKhoan,email,'.$taikhoan->maTK.',maTK',
            'soDienThoai' => 'required|string|max:15',
            'diaChi' => 'nullable|string|max:255',
            'maVT' => 'required|exists:VaiTro,maVT',
            'trangThai' => 'required|in:HOAT_DONG,KHOA',
        ]);

        $data = $request->only('hoTen', 'email', 'soDienThoai', 'diaChi', 'maVT', 'trangThai');

        if ($request->filled('matKhau')) {
            $data['matKhau'] = $request->matKhau;
        }

        $taikhoan->update($data);

        return redirect()->route('admin.taikhoan.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function toggleStatus(TaiKhoan $taikhoan)
    {
        $taikhoan->trangThai = $taikhoan->trangThai === 'HOAT_DONG' ? 'KHOA' : 'HOAT_DONG';
        $taikhoan->save();

        return redirect()->route('admin.taikhoan.index')->with('success', 'Cập nhật trạng thái thành công!');
    }
}
