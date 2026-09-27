@extends('layouts.management')
@section('title', 'Thêm tài khoản')
@section('content')
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('admin.taikhoan.index') }}" class="hover:text-bhx-600 transition">Tài khoản</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">Thêm mới</span>
</nav>

<div class="mb-5">
    <h1 class="text-2xl font-bold text-gray-800">Thêm tài khoản mới</h1>
    <p class="text-sm text-gray-500 mt-1">Tạo tài khoản quản trị, nhân viên hoặc khách hàng</p>
</div>

<div class="bg-white rounded-xl shadow-sm p-5 max-w-2xl">
    <form method="POST" action="{{ route('admin.taikhoan.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên <span class="text-red-500">*</span></label>
                <input type="text" name="hoTen" value="{{ old('hoTen') }}" placeholder="VD: Nguyễn Văn A" class="bhx-input @error('hoTen') !border-red-500 @enderror" required>
                @error('hoTen') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập <span class="text-red-500">*</span></label>
                <input type="text" name="tenDangNhap" value="{{ old('tenDangNhap') }}" placeholder="VD: nhanvien1" class="bhx-input @error('tenDangNhap') !border-red-500 @enderror" required>
                @error('tenDangNhap') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="VD: a@example.com" class="bhx-input @error('email') !border-red-500 @enderror" required>
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                <input type="text" name="soDienThoai" value="{{ old('soDienThoai') }}" placeholder="VD: 0912345678" class="bhx-input @error('soDienThoai') !border-red-500 @enderror" required>
                @error('soDienThoai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                <input type="text" name="diaChi" value="{{ old('diaChi') }}" class="bhx-input">
            </div>
            <div>
                <x-searchable-select name="maVT" label="Vai trò" :required="true"
                    placeholder="-- Chọn vai trò --" searchPlaceholder="Gõ để tìm vai trò..."
                    :options="$vaiTros->map(fn ($vt) => ['value' => $vt->maVT, 'label' => $vt->tenVT])->all()"
                    :selected="old('maVT')" :error="$errors->first('maVT')" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu <span class="text-red-500">*</span></label>
                <div class="pw-wrap">
                    <input type="password" name="matKhau" class="bhx-input pr-10 @error('matKhau') !border-red-500 @enderror" required>
                    <span class="pw-toggle" onclick="togglePw(this)">👁</span>
                </div>
                @error('matKhau') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                <select name="trangThai" class="bhx-input" required>
                    <option value="HOAT_DONG" {{ old('trangThai', 'HOAT_DONG') == 'HOAT_DONG' ? 'selected' : '' }}>Hoạt động</option>
                    <option value="KHOA" {{ old('trangThai') == 'KHOA' ? 'selected' : '' }}>Khóa</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2 mt-4">
            <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Thêm</button>
            <a href="{{ route('admin.taikhoan.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Hủy</a>
        </div>
    </form>
</div>
@endsection
