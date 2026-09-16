@extends('layouts.app')
@section('title', 'Đặt hàng')
@section('content')
<h1 class="text-2xl font-bold mb-4 flex items-center gap-2">
    <i class="bi bi-bag-check text-bhx-500"></i> Xác nhận đặt hàng
</h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bhx-card p-5">
        <h2 class="font-bold text-gray-800 mb-3"><i class="bi bi-basket text-bhx-500"></i> Sản phẩm trong giỏ</h2>
        @foreach($gioHang->chiTietGioHangs as $ct)
        <div class="flex justify-between items-center py-2.5 border-b hover:bg-bhx-50/50 px-2 rounded">
            <div>
                <p class="font-medium text-sm">{{ $ct->sanPham->tenSP }}</p>
                <p class="text-xs text-gray-500">{{ number_format($ct->donGia, 0, ',', '.') }}đ x {{ $ct->soLuong }} {{ $ct->sanPham->donVi ?? 'kg' }}</p>
            </div>
            <span class="font-medium text-sm">{{ number_format($ct->thanhTien, 0, ',', '.') }}đ</span>
        </div>
        @endforeach
        <div class="mt-4 text-right">
            <span class="text-sm text-gray-500">Tổng tiền: </span>
            <span class="text-xl font-extrabold text-bhx-orange">{{ number_format($gioHang->tongTien, 0, ',', '.') }}đ</span>
        </div>
    </div>

    <div class="bhx-card p-5">
        <form method="POST" action="{{ route('donhang.place') }}">
            @csrf
            <h2 class="font-bold text-gray-800 mb-3"><i class="bi bi-truck text-bhx-500"></i> Thông tin giao hàng</h2>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên người nhận *</label>
                <input type="text" name="tenNguoiNhan" value="{{ Auth::user()->hoTen }}" class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-bhx-500 focus:outline-none @error('tenNguoiNhan') border-red-500 @enderror" required>
                @error('tenNguoiNhan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại *</label>
                <input type="text" name="soDienThoai" value="{{ Auth::user()->soDienThoai }}" class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-bhx-500 focus:outline-none @error('soDienThoai') border-red-500 @enderror" required>
                @error('soDienThoai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ *</label>
                <input type="text" name="diaChi" value="{{ Auth::user()->diaChi }}" class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-bhx-500 focus:outline-none @error('diaChi') border-red-500 @enderror" required>
                @error('diaChi') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bhx-btn-orange"><i class="bi bi-check2-square"></i> Đặt hàng</button>
                <a href="{{ route('giohang.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-300 transition">Quay lại giỏ</a>
            </div>
        </form>
    </div>
</div>
@endsection