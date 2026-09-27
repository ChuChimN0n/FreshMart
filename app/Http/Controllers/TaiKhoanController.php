<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\Models\VaiTro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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
        if ($request->filled('trangThai') && in_array($request->trangThai, ['HOAT_DONG', 'KHOA'], true)) {
            $query->where('trangThai', $request->trangThai);
        }
        $taiKhoans = $query->orderBy('maTK', 'desc')->paginate(10);
        $vaiTros = VaiTro::orderBy('tenVT')->get();

        $thongKe = [
            'tong' => TaiKhoan::count(),
            'admin' => TaiKhoan::where('maVT', VaiTro::ADMIN_ID)->count(),
            'staff' => TaiKhoan::where('maVT', VaiTro::STAFF_ID)->count(),
            'khach' => TaiKhoan::where('maVT', VaiTro::KHACH_HANG_ID)->count(),
            'khoa' => TaiKhoan::where('trangThai', 'KHOA')->count(),
        ];

        return view('admin.taikhoan.index', compact('taiKhoans', 'vaiTros', 'thongKe'));
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
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:100',
                'unique:TaiKhoan,email',
            ],
            'soDienThoai' => [
                'required',
                'string',
                'unique:TaiKhoan,soDienThoai',
                'regex:/^(03|05|07|08|09)[0-9]{8}$/',
            ],
            'diaChi' => 'nullable|string|max:255',
            'maVT' => 'required|exists:VaiTro,maVT',
            'matKhau' => [
                'required',
                Password::min(6),
                'regex:/^\S*$/',
            ],
            'trangThai' => 'required|in:HOAT_DONG,KHOA',
        ], [
            'tenDangNhap.unique' => 'Tên đăng nhập này đã được sử dụng, vui lòng chọn tên khác.',
            'email.required' => 'Vui lòng nhập Email.',
            'soDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'email.email' => 'Địa chỉ email không hợp lệ hoặc không tồn tại.',
            'email.unique' => 'Email này đã được sử dụng.',
            'soDienThoai.unique' => 'Số điện thoại này đã được sử dụng.',
            'soDienThoai.regex' => 'Số điện thoại không đúng định dạng (phải gồm 10 chữ số hợp lệ tại Việt Nam).',
            'matKhau.required' => 'Vui lòng nhập mật khẩu.',
            'matKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'matKhau.regex' => 'Mật khẩu không được chứa khoảng trắng.',
        ]);

        abort_if((int) $request->maVT === VaiTro::ADMIN_ID && ! Auth::user()->isAdmin(), 403);

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

    public function update(Request $request, TaiKhoan $taikhoan): RedirectResponse
    {
        if ($taikhoan->isCustomer()) {
            return redirect()->route('admin.taikhoan.index')->with('error', 'Không thể sửa thông tin khách hàng!');
        }

        $request->validate([
            'hoTen' => 'required|string|max:100',
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:100',
                Rule::unique('TaiKhoan', 'email')->ignore($taikhoan->maTK, 'maTK'),
            ],
            'soDienThoai' => [
                'required',
                'string',
                Rule::unique('TaiKhoan', 'soDienThoai')->ignore($taikhoan->maTK, 'maTK'),
                'regex:/^(03|05|07|08|09)[0-9]{8}$/',
            ],
            'diaChi' => 'nullable|string|max:255',
            'maVT' => 'required|exists:VaiTro,maVT',
            'matKhau' => [
                'nullable',
                Password::min(6),
                'regex:/^\S*$/',
            ],
            'trangThai' => 'required|in:HOAT_DONG,KHOA',
        ], [
            'email.required' => 'Vui lòng nhập Email.',
            'soDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'email.email' => 'Địa chỉ email không hợp lệ hoặc không tồn tại.',
            'email.unique' => 'Email này đã được sử dụng.',
            'soDienThoai.unique' => 'Số điện thoại này đã được sử dụng.',
            'soDienThoai.regex' => 'Số điện thoại không đúng định dạng (phải gồm 10 chữ số hợp lệ tại Việt Nam).',
            'matKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'matKhau.regex' => 'Mật khẩu không được chứa khoảng trắng.',
        ]);

        $data = $request->only('hoTen', 'email', 'soDienThoai', 'diaChi', 'maVT', 'trangThai');

        if ($request->filled('matKhau')) {
            $data['matKhau'] = $request->matKhau;
        }

        try {
            DB::transaction(function () use ($taikhoan, $data): void {
                VaiTro::whereKey(VaiTro::ADMIN_ID)->lockForUpdate()->firstOrFail();
                $account = TaiKhoan::whereKey($taikhoan->maTK)->lockForUpdate()->firstOrFail();
                $this->guardAccountChange($account, (int) $data['maVT'], $data['trangThai']);
                $account->update($data);
            }, 3);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.taikhoan.index')->with('success', 'Cập nhật tài khoản thành công!');
    }

    public function toggleStatus(TaiKhoan $taikhoan): RedirectResponse
    {
        try {
            DB::transaction(function () use ($taikhoan): void {
                VaiTro::whereKey(VaiTro::ADMIN_ID)->lockForUpdate()->firstOrFail();
                $account = TaiKhoan::whereKey($taikhoan->maTK)->lockForUpdate()->firstOrFail();
                $status = $account->isActive() ? 'KHOA' : 'HOAT_DONG';
                $this->guardAccountChange($account, $account->maVT, $status);
                $account->update(['trangThai' => $status]);
            }, 3);
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.taikhoan.index')->with('success', 'Cập nhật trạng thái thành công!');
    }

    private function guardAccountChange(TaiKhoan $account, int $role, string $status): void
    {
        abort_if(($account->isAdmin() || $role === VaiTro::ADMIN_ID) && ! Auth::user()->isAdmin(), 403);

        if ($account->maTK === Auth::id() && ($role !== $account->maVT || $status !== 'HOAT_DONG')) {
            throw new \DomainException('Không thể tự khóa hoặc thay đổi vai trò của chính mình!');
        }

        if ($account->isAdmin() && $account->isActive() && ($role !== VaiTro::ADMIN_ID || $status !== 'HOAT_DONG')) {
            $admins = TaiKhoan::where('maVT', VaiTro::ADMIN_ID)->where('trangThai', 'HOAT_DONG')->lockForUpdate()->get();
            if ($admins->count() <= 1) {
                throw new \DomainException('Phải giữ ít nhất một tài khoản quản lý đang hoạt động!');
            }
        }
    }
}
