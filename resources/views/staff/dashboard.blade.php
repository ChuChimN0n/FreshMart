@extends('layouts.app')
@section('title', 'Staff Dashboard')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Dashboard Nhân viên</h1>
    <p class="text-gray-600">Xin chào, {{ Auth::user()->hoTen }}</p>
</div>

<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm text-gray-500">Tổng sản phẩm</h3>
        <p class="text-2xl font-bold text-bhx-600">{{ $tongSanPham }}</p>
        <p class="text-xs text-gray-400">Còn hàng: {{ $sanPhamConHang }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm text-gray-500">Tổng đơn hàng</h3>
        <p class="text-2xl font-bold text-blue-600">{{ $tongDonHang }}</p>
        <p class="text-xs text-gray-400">Chờ xác nhận: {{ $donChoXacNhan }}</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-bold mb-3">Đơn hàng mới nhất</h3>
        @forelse($donHangMoiNhat as $dh)
        <div class="flex justify-between items-center py-2 border-b text-sm">
            <div>
                <span class="font-medium">#{{ $dh->maDH }}</span>
                <span class="text-gray-500">{{ $dh->taiKhoan->hoTen ?? '' }}</span>
            </div>
            <a href="{{ route('staff.donhang.detail', $dh) }}" class="text-blue-600 hover:underline text-xs">Xem</a>
        </div>
        @empty
        <p class="text-gray-500 text-sm">Chưa có đơn hàng</p>
        @endforelse
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-bold mb-3">Quản lý nhanh</h3>
        <div class="space-y-2">
            <a href="{{ route('staff.sanpham.index') }}" class="block bg-bhx-50 p-3 rounded hover:bg-bhx-100 text-sm font-medium text-bhx-700">Quản lý sản phẩm</a>
            <a href="{{ route('staff.danhmuc.index') }}" class="block bg-blue-50 p-3 rounded hover:bg-blue-100 text-sm font-medium text-blue-700">Quản lý danh mục</a>
            <a href="{{ route('staff.donhang.index') }}" class="block bg-purple-50 p-3 rounded hover:bg-purple-100 text-sm font-medium text-purple-700">Quản lý đơn hàng</a>
            <a href="{{ route('staff.danhgia.index') }}" class="block bg-orange-50 p-3 rounded hover:bg-orange-100 text-sm font-medium text-orange-700">Quản lý đánh giá</a>
        </div>
    </div>
</div>
@endsection
