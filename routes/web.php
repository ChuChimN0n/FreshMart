<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaoCaoController;
use App\Http\Controllers\DanhGiaController;
use App\Http\Controllers\DanhMucController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonHangController;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NhaCungCapController;
use App\Http\Controllers\SanPhamController;
use App\Http\Controllers\TaiKhoanController;
use App\Http\Controllers\VaiTroController;
use App\Models\VaiTro;
use Illuminate\Support\Facades\Route;

// ========== PUBLIC ==========
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/san-pham/{sanpham}', [HomeController::class, 'show'])->name('home.show');

// ========== AUTH ==========
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// ========== GIO HANG (login required) ==========
Route::middleware('auth')->group(function () {
    Route::get('/ho-so', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/ho-so', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::get('/doi-mat-khau', [AuthController::class, 'showChangePasswordForm'])->name('change-password');
    Route::post('/doi-mat-khau', [AuthController::class, 'changePassword']);

    Route::get('/gio-hang', [GioHangController::class, 'index'])->name('giohang.index');
    Route::post('/gio-hang/them', [GioHangController::class, 'add'])->name('giohang.add');
    Route::put('/gio-hang/{chitiet}', [GioHangController::class, 'update'])->name('giohang.update');
    Route::delete('/gio-hang/{chitiet}', [GioHangController::class, 'remove'])->name('giohang.remove');

    // Don hang
    Route::get('/dat-hang', [DonHangController::class, 'checkout'])->name('checkout');
    Route::post('/dat-hang', [DonHangController::class, 'placeOrder'])->name('donhang.place');
    Route::get('/don-hang', [DonHangController::class, 'myOrders'])->name('donhang.index');
    Route::get('/don-hang/{donhang}', [DonHangController::class, 'myOrderDetail'])->name('donhang.detail');
    Route::patch('/don-hang/{donhang}/huy', [DonHangController::class, 'cancel'])->name('donhang.cancel');

    // Danh gia
    Route::post('/danh-gia', [DanhGiaController::class, 'store'])->name('danhgia.store');
});

// ========== ADMIN (role:1) ==========
Route::prefix('admin')->name('admin.')->middleware(['role:'.VaiTro::ADMIN_ID])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // Nha cung cap
    Route::get('/nha-cung-cap', [NhaCungCapController::class, 'index'])->name('nhacungcap.index');
    Route::get('/nha-cung-cap/them', [NhaCungCapController::class, 'create'])->name('nhacungcap.create');
    Route::post('/nha-cung-cap/them', [NhaCungCapController::class, 'store'])->name('nhacungcap.store');
    Route::get('/nha-cung-cap/{nhacungcap}/sua', [NhaCungCapController::class, 'edit'])->name('nhacungcap.edit');
    Route::put('/nha-cung-cap/{nhacungcap}', [NhaCungCapController::class, 'update'])->name('nhacungcap.update');

    // Tai khoan
    Route::get('/tai-khoan', [TaiKhoanController::class, 'index'])->name('taikhoan.index');
    Route::get('/tai-khoan/them', [TaiKhoanController::class, 'create'])->name('taikhoan.create');
    Route::post('/tai-khoan/them', [TaiKhoanController::class, 'store'])->name('taikhoan.store');
    Route::get('/tai-khoan/{taikhoan}/sua', [TaiKhoanController::class, 'edit'])->name('taikhoan.edit');
    Route::put('/tai-khoan/{taikhoan}', [TaiKhoanController::class, 'update'])->name('taikhoan.update');
    Route::patch('/tai-khoan/{taikhoan}/trang-thai', [TaiKhoanController::class, 'toggleStatus'])->name('taikhoan.toggle');

    // Vai tro
    Route::get('/vai-tro', [VaiTroController::class, 'index'])->name('vaitro.index');
    Route::get('/vai-tro/them', [VaiTroController::class, 'create'])->name('vaitro.create');
    Route::post('/vai-tro/them', [VaiTroController::class, 'store'])->name('vaitro.store');
    Route::get('/vai-tro/{vaitro}/sua', [VaiTroController::class, 'edit'])->name('vaitro.edit');
    Route::put('/vai-tro/{vaitro}', [VaiTroController::class, 'update'])->name('vaitro.update');

    // Bao cao
    Route::get('/bao-cao', [BaoCaoController::class, 'index'])->name('baocao.index');
    Route::get('/bao-cao/san-pham', [BaoCaoController::class, 'sanPham'])->name('baocao.sanpham');
    Route::get('/bao-cao/doanh-thu', [BaoCaoController::class, 'doanhThu'])->name('baocao.doanhthu');
    Route::get('/bao-cao/don-hang', [BaoCaoController::class, 'donHang'])->name('baocao.donhang');
});

// ========== STAFF (role:2) ==========
Route::prefix('staff')->name('staff.')->middleware(['role:'.VaiTro::STAFF_ID])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'staffDashboard'])->name('dashboard');

    // Danh muc
    Route::get('/danh-muc', [DanhMucController::class, 'index'])->name('danhmuc.index');
    Route::get('/danh-muc/them', [DanhMucController::class, 'create'])->name('danhmuc.create');
    Route::post('/danh-muc/them', [DanhMucController::class, 'store'])->name('danhmuc.store');
    Route::get('/danh-muc/{danhmuc}/sua', [DanhMucController::class, 'edit'])->name('danhmuc.edit');
    Route::put('/danh-muc/{danhmuc}', [DanhMucController::class, 'update'])->name('danhmuc.update');

    // San pham
    Route::get('/san-pham', [SanPhamController::class, 'index'])->name('sanpham.index');
    Route::get('/san-pham/them', [SanPhamController::class, 'create'])->name('sanpham.create');
    Route::post('/san-pham/them', [SanPhamController::class, 'store'])->name('sanpham.store');
    Route::get('/san-pham/{sanpham}/sua', [SanPhamController::class, 'edit'])->name('sanpham.edit');
    Route::put('/san-pham/{sanpham}', [SanPhamController::class, 'update'])->name('sanpham.update');

    // Don hang
    Route::get('/don-hang', [DonHangController::class, 'staffIndex'])->name('donhang.index');
    Route::get('/don-hang/{donhang}', [DonHangController::class, 'staffDetail'])->name('donhang.detail');
    Route::patch('/don-hang/{donhang}/trang-thai', [DonHangController::class, 'updateStatus'])->name('donhang.update');

    // Danh gia
    Route::get('/danh-gia', [DanhGiaController::class, 'staffIndex'])->name('danhgia.index');
});
