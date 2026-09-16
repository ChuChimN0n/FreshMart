@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('content')
<div class="max-w-md mx-auto">
    <div class="rounded-2xl overflow-hidden shadow-md">
        <div class="bg-bhx-700 px-6 py-5 text-center">
            <span class="w-10 h-10 mx-auto rounded-lg bg-white text-bhx-600 flex items-center justify-center mb-2"><i class="bi bi-key text-xl"></i></span>
            <h1 class="text-lg font-extrabold text-white tracking-wide">Đổi mật khẩu</h1>
        </div>

        <div class="bg-white p-6">
            <form method="POST" action="{{ route('change-password') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu hiện tại *</label>
                    <div class="pw-wrap">
                        <input type="password" name="matKhau_hien_tai"
                               class="w-full border rounded-lg px-3 py-2.5 pr-10 focus:outline-none focus:ring-2 focus:ring-bhx-500 @error('matKhau_hien_tai') border-red-500 @enderror"
                               required>
                        <span class="pw-toggle" onclick="togglePw(this)">👁</span>
                    </div>
                    @error('matKhau_hien_tai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới *</label>
                    <div class="pw-wrap">
                        <input type="password" name="matKhau_moi"
                               class="w-full border rounded-lg px-3 py-2.5 pr-10 focus:outline-none focus:ring-2 focus:ring-bhx-500 @error('matKhau_moi') border-red-500 @enderror"
                               required>
                        <span class="pw-toggle" onclick="togglePw(this)">👁</span>
                    </div>
                    @error('matKhau_moi') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu mới *</label>
                    <div class="pw-wrap">
                        <input type="password" name="matKhau_moi_confirmation"
                               class="w-full border rounded-lg px-3 py-2.5 pr-10 focus:outline-none focus:ring-2 focus:ring-bhx-500"
                               required>
                        <span class="pw-toggle" onclick="togglePw(this)">👁</span>
                    </div>
                </div>

                <button type="submit" class="w-full bhx-btn-primary !py-3">
                    <i class="bi bi-shield-lock"></i> Đổi mật khẩu
                </button>
            </form>
        </div>
    </div>
</div>
@endsection