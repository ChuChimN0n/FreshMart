@extends('layouts.app')
@section('title', $taiKhoan->isCustomer() ? 'Thông tin tài khoản' : 'Sửa tài khoản')
@section('content')
<h1 class="text-2xl font-bold mb-4">{{ $taiKhoan->isCustomer() ? 'Thông tin tài khoản' : 'Sửa tài khoản' }}</h1>
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    @if($taiKhoan->isCustomer())
        {{-- KHÁCH HÀNG: Chỉ xem thông tin --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
                <input type="text" value="{{ $taiKhoan->hoTen }}" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                <input type="text" value="{{ $taiKhoan->tenDangNhap }}" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" value="{{ $taiKhoan->email }}" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
                <input type="text" value="{{ $taiKhoan->soDienThoai }}" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                <input type="text" value="{{ $taiKhoan->diaChi }}" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                <input type="text" value="{{ $taiKhoan->vaiTro->tenVT ?? '' }}" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <input type="text" value="{{ $taiKhoan->trangThai == 'HOAT_DONG' ? 'Hoạt động' : 'Khóa' }}" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.taikhoan.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Quay lại</a>
        </div>
    @else
        {{-- NHÂN VIÊN / ADMIN: Sửa tất cả + đổi MK --}}
        <form method="POST" action="{{ route('admin.taikhoan.update', $taiKhoan) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên *</label>
                    <input type="text" name="hoTen" value="{{ old('hoTen', $taiKhoan->hoTen) }}" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                    <input type="text" value="{{ $taiKhoan->tenDangNhap }}" class="w-full border rounded px-3 py-2 bg-gray-100" disabled>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $taiKhoan->email) }}" class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror" required>
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                    <input type="text" name="soDienThoai" value="{{ old('soDienThoai', $taiKhoan->soDienThoai) }}" class="w-full border rounded px-3 py-2 @error('soDienThoai') border-red-500 @enderror" required>
                    @error('soDienThoai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                    <input type="text" name="diaChi" value="{{ old('diaChi', $taiKhoan->diaChi) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò *</label>
                    <select name="maVT" class="w-full border rounded px-3 py-2" required>
                        @foreach($vaiTros as $vt)
                        <option value="{{ $vt->maVT }}" {{ old('maVT', $taiKhoan->maVT) == $vt->maVT ? 'selected' : '' }}>{{ $vt->tenVT }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới (để trống nếu không đổi)</label>
                    <div class="pw-wrap">
                        <input type="password" name="matKhau" class="w-full border rounded px-3 py-2 pr-10">
                        <span class="pw-toggle" onclick="togglePw(this)">👁</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái *</label>
                    <select name="trangThai" class="w-full border rounded px-3 py-2" required>
                        <option value="HOAT_DONG" {{ old('trangThai', $taiKhoan->trangThai) == 'HOAT_DONG' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="KHOA" {{ old('trangThai', $taiKhoan->trangThai) == 'KHOA' ? 'selected' : '' }}>Khóa</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">Cập nhật</button>
                <a href="{{ route('admin.taikhoan.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Hủy</a>
            </div>
        </form>
    @endif
</div>
@endsection
