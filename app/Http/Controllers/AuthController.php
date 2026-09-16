<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'tenDangNhap' => 'required',
            'matKhau' => 'required',
        ]);

        $taiKhoan = TaiKhoan::where('tenDangNhap', $credentials['tenDangNhap'])
            ->orWhere('email', $credentials['tenDangNhap'])
            ->first();

        if (! $taiKhoan || ! Hash::check($credentials['matKhau'], $taiKhoan->matKhau)) {
            return back()->withErrors(['tenDangNhap' => 'Tên đăng nhập hoặc mật khẩu không đúng'])->onlyInput('tenDangNhap');
        }

        if ($taiKhoan->trangThai !== 'HOAT_DONG') {
            return back()->withErrors(['tenDangNhap' => 'Tài khoản đã bị khóa'])->onlyInput('tenDangNhap');
        }

        Auth::login($taiKhoan, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectByRole();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'hoTen' => 'required|string|max:100',
            'tenDangNhap' => 'required|string|max:100|unique:TaiKhoan,tenDangNhap',
            'email' => 'required|email|max:100|unique:TaiKhoan,email',
            'soDienThoai' => 'required|string|max:15',
            'diaChi' => 'required|string|max:255',
            'matKhau' => ['required', 'confirmed', Password::min(6)],
        ]);

        $taiKhoan = TaiKhoan::create([
            'maVT' => VaiTro::KHACH_HANG_ID,
            'hoTen' => $request->hoTen,
            'tenDangNhap' => $request->tenDangNhap,
            'matKhau' => $request->matKhau,
            'email' => $request->email,
            'soDienThoai' => $request->soDienThoai,
            'diaChi' => $request->diaChi,
            'trangThai' => 'HOAT_DONG',
        ]);

        // Tao gio hang trong cho khach hang moi
        $taiKhoan->gioHang()->create(['tongTien' => 0]);

        Auth::login($taiKhoan);
        $request->session()->regenerate();

        return redirect('/')->with('success', 'Đăng ký thành công!');
    }

    public function showProfile()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'hoTen' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:TaiKhoan,email,'.$user->maTK.',maTK',
            'soDienThoai' => 'required|string|max:15',
            'diaChi' => 'required|string|max:255',
        ]);

        $user->update($request->only('hoTen', 'email', 'soDienThoai', 'diaChi'));

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'matKhau_hien_tai' => 'required',
            'matKhau_moi' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user = Auth::user();

        if (! Hash::check($request->matKhau_hien_tai, $user->matKhau)) {
            return back()->withErrors(['matKhau_hien_tai' => 'Mật khẩu hiện tại không đúng']);
        }

        $user->update(['matKhau' => $request->matKhau_moi]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    private function redirectByRole()
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect('/admin/dashboard');
        }
        if ($user->isStaff()) {
            return redirect('/staff/dashboard');
        }

        return redirect('/');
    }
}
