@extends('layouts.management')
@section('title', $taiKhoan->isCustomer() ? 'Thông tin tài khoản' : 'Sửa tài khoản')
@section('content')
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('admin.taikhoan.index') }}" class="hover:text-bhx-600 transition">Tài khoản</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">{{ $taiKhoan->hoTen }}</span>
</nav>

<div class="mb-5 flex items-center gap-4">
    <span class="w-14 h-14 rounded-full bg-bhx-100 text-bhx-700 flex items-center justify-center text-2xl font-bold shrink-0">
        {{ mb_strtoupper(mb_substr($taiKhoan->hoTen, 0, 1)) }}
    </span>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $taiKhoan->isCustomer() ? 'Thông tin tài khoản' : 'Sửa tài khoản' }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $taiKhoan->vaiTro->tenVT ?? '' }} · {{ $taiKhoan->trangThai == 'HOAT_DONG' ? 'Hoạt động' : 'Bị khóa' }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-5 max-w-2xl">
    @if($taiKhoan->isCustomer())
        {{-- KHÁCH HÀNG: Chỉ xem thông tin --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach([['Họ tên', $taiKhoan->hoTen], ['Tên đăng nhập', $taiKhoan->tenDangNhap], ['Email', $taiKhoan->email], ['Số điện thoại', $taiKhoan->soDienThoai], ['Địa chỉ', $taiKhoan->diaChi ?: '—'], ['Vai trò', $taiKhoan->vaiTro->tenVT ?? ''], ['Trạng thái', $taiKhoan->trangThai == 'HOAT_DONG' ? 'Hoạt động' : 'Bị khóa']] as [$lb, $val])
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-400 mb-1">{{ $lb }}</label>
                    <p class="bhx-input bg-gray-50">{{ $val }}</p>
                </div>
            @endforeach
        </div>
        <div class="flex gap-2 mt-4">
            <a href="{{ route('admin.taikhoan.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Quay lại</a>
        </div>
    @else
        {{-- NHÂN VIÊN / ADMIN: Sửa tất cả + đổi MK --}}
        <form method="POST" action="{{ route('admin.taikhoan.update', $taiKhoan) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên <span class="text-red-500">*</span></label>
                    <input type="text" name="hoTen" value="{{ old('hoTen', $taiKhoan->hoTen) }}" class="bhx-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                    <input type="text" value="{{ $taiKhoan->tenDangNhap }}" class="bhx-input bg-gray-50" disabled>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $taiKhoan->email) }}" class="bhx-input @error('email') !border-red-500 @enderror" required>
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                    <input type="text" name="soDienThoai" value="{{ old('soDienThoai', $taiKhoan->soDienThoai) }}" class="bhx-input @error('soDienThoai') !border-red-500 @enderror" required>
                    @error('soDienThoai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                    <input type="text" name="diaChi" value="{{ old('diaChi', $taiKhoan->diaChi) }}" class="bhx-input">
                </div>
                <div>
                    <x-searchable-select name="maVT" label="Vai trò" :required="true"
                        placeholder="-- Chọn vai trò --" searchPlaceholder="Gõ để tìm vai trò..."
                        :options="$vaiTros->map(fn ($vt) => ['value' => $vt->maVT, 'label' => $vt->tenVT])->all()"
                        :selected="old('maVT', $taiKhoan->maVT)" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới (để trống nếu không đổi)</label>
                    <div class="pw-wrap">
                        <input type="password" name="matKhau" class="bhx-input pr-10">
                        <span class="pw-toggle" onclick="togglePw(this)">👁</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="trangThai" class="bhx-input" required>
                        <option value="HOAT_DONG" {{ old('trangThai', $taiKhoan->trangThai) == 'HOAT_DONG' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="KHOA" {{ old('trangThai', $taiKhoan->trangThai) == 'KHOA' ? 'selected' : '' }}>Khóa</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Cập nhật</button>
                <a href="{{ route('admin.taikhoan.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Hủy</a>
            </div>
        </form>
    @endif
</div>
@endsection
