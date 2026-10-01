<?php

namespace App\Http\Controllers;

use App\Models\TaiKhoan;
use App\Models\VaiTro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
            'tenDangNhap' => 'required|string|max:100',
            'matKhau' => 'required|string',
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
            'diaChi' => 'required|string|max:255',
            'matKhau' => [
                'required',
                'confirmed',
                Password::min(6),
                'regex:/^\S*$/',
            ],
        ], [
            'tenDangNhap.unique' => 'Tên đăng nhập này đã được sử dụng, vui lòng chọn tên khác.',
            'email.required' => 'Vui lòng nhập Email.',
            'soDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'email.email' => 'Địa chỉ email không hợp lệ hoặc không tồn tại.',
            'email.unique' => 'Email này đã được sử dụng.',
            'soDienThoai.unique' => 'Số điện thoại này đã được sử dụng.',
            'soDienThoai.regex' => 'Số điện thoại không đúng định dạng (phải gồm 10 chữ số hợp lệ tại Việt Nam).',
            'matKhau.required' => 'Vui lòng nhập mật khẩu.',
            'matKhau.confirmed' => 'Xác nhận mật khẩu không trùng khớp.',
            'matKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'matKhau.regex' => 'Mật khẩu không được chứa khoảng trắng.',
        ]);

        $taiKhoan = DB::transaction(function () use ($request): TaiKhoan {
            $taiKhoan = TaiKhoan::create([
                'maVT' => VaiTro::KHACH_HANG_ID,
                'hoTen' => $request->hoTen,
                'tenDangNhap' => $request->tenDangNhap,
                'matKhau' => Hash::make($request->matKhau),
                'email' => $request->email,
                'soDienThoai' => $request->soDienThoai,
                'diaChi' => $request->diaChi,
                'trangThai' => 'HOAT_DONG',
            ]);

            $taiKhoan->gioHang()->create(['tongTien' => 0]);

            return $taiKhoan;
        });

        return redirect()->route('login')
            ->with('success', 'Đăng ký thành công! Vui lòng đăng nhập.')
            ->withInput(['tenDangNhap' => $taiKhoan->tenDangNhap]);
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
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:100',
                Rule::unique('TaiKhoan', 'email')->ignore($user->maTK, 'maTK'),
            ],
            'soDienThoai' => [
                'required',
                'string',
                Rule::unique('TaiKhoan', 'soDienThoai')->ignore($user->maTK, 'maTK'),
                'regex:/^(03|05|07|08|09)[0-9]{8}$/',
            ],
            'diaChi' => 'required|string|max:255',
        ], [
            'email.required' => 'Vui lòng nhập Email.',
            'soDienThoai.required' => 'Vui lòng nhập số điện thoại.',
            'email.email' => 'Địa chỉ email không hợp lệ hoặc không tồn tại.',
            'email.unique' => 'Email này đã được sử dụng.',
            'soDienThoai.unique' => 'Số điện thoại này đã được sử dụng.',
            'soDienThoai.regex' => 'Số điện thoại không đúng định dạng (phải gồm 10 chữ số hợp lệ tại Việt Nam).',
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
            'matKhau_moi' => [
                'required',
                'confirmed',
                Password::min(6),
                'regex:/^\S*$/',
            ],
        ], [
            'matKhau_hien_tai.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'matKhau_moi.required' => 'Vui lòng nhập mật khẩu mới.',
            'matKhau_moi.confirmed' => 'Xác nhận mật khẩu mới không trùng khớp.',
            'matKhau_moi.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'matKhau_moi.regex' => 'Mật khẩu mới không được chứa khoảng trắng.',
        ]);

        $user = Auth::user();

        if (! Hash::check($request->matKhau_hien_tai, $user->matKhau)) {
            return back()->withErrors(['matKhau_hien_tai' => 'Mật khẩu hiện tại không đúng']);
        }

        $user->update(['matKhau' => Hash::make($request->matKhau_moi)]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    private function redirectByRole(): RedirectResponse
    {
        return redirect()->route(Auth::user()->dashboardRoute());
    }
}
