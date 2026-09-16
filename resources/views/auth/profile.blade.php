@extends('layouts.app')
@section('title', 'Thông tin cá nhân')
@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Thông tin cá nhân</h1>

    @if(session('success'))
        <div class="bg-bhx-50 border border-bhx-400 text-bhx-700 px-4 py-3 rounded mb-4">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            ✗ {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="font-bold mb-4">Thông tin hiện tại</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Họ tên:</span>
                <p class="font-medium">{{ $user->hoTen }}</p>
            </div>
            <div>
                <span class="text-gray-500">Tên đăng nhập:</span>
                <p class="font-medium">{{ $user->tenDangNhap }}</p>
            </div>
            <div>
                <span class="text-gray-500">Email:</span>
                <p class="font-medium">{{ $user->email }}</p>
            </div>
            <div>
                <span class="text-gray-500">Số điện thoại:</span>
                <p class="font-medium">{{ $user->soDienThoai }}</p>
            </div>
            <div class="col-span-2">
                <span class="text-gray-500">Địa chỉ:</span>
                <p class="font-medium">{{ $user->diaChi }}</p>
            </div>
            <div>
                <span class="text-gray-500">Vai trò:</span>
                <p class="font-medium">{{ $user->vaiTro->tenVT ?? '' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="font-bold mb-4">Chỉnh sửa thông tin</h2>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên *</label>
                <input type="text" name="hoTen" value="{{ old('hoTen', $user->hoTen) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bhx-500 @error('hoTen') border-red-500 @enderror" required>
                @error('hoTen') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bhx-500 @error('email') border-red-500 @enderror" required>
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                <input type="text" name="soDienThoai" value="{{ old('soDienThoai', $user->soDienThoai) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bhx-500 @error('soDienThoai') border-red-500 @enderror" required>
                @error('soDienThoai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ *</label>
                <input type="text" name="diaChi" value="{{ old('diaChi', $user->diaChi) }}"
                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-bhx-500 @error('diaChi') border-red-500 @enderror" required>
                @error('diaChi') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-2">
                <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Cập nhật</button>
                <a href="{{ route('change-password') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Đổi mật khẩu</a>
            </div>
        </form>
    </div>
</div>
@endsection
