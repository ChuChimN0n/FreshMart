@extends('layouts.app')
@section('title', 'Báo cáo thống kê')
@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Báo cáo thống kê</h1>
    <p class="text-gray-600">Chọn loại báo cáo bạn muốn xem</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <a href="{{ route('admin.baocao.sanpham', ['tuNgay' => now()->subDays(30)->format('Y-m-d'), 'denNgay' => now()->format('Y-m-d')]) }}"
       class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition group">
        <div class="text-4xl mb-3">📦</div>
        <h3 class="text-lg font-bold text-gray-800 group-hover:text-bhx-600">Thống kê sản phẩm</h3>
        <p class="text-sm text-gray-500 mt-2">Xem số lượng sản phẩm đã bán, sản phẩm bán chạy trong khoảng thời gian.</p>
        <div class="mt-4 text-bhx-600 font-medium text-sm">Xem báo cáo →</div>
    </a>

    <a href="{{ route('admin.baocao.doanhthu', ['tuNgay' => now()->subDays(30)->format('Y-m-d'), 'denNgay' => now()->format('Y-m-d')]) }}"
       class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition group">
        <div class="text-4xl mb-3">💰</div>
        <h3 class="text-lg font-bold text-gray-800 group-hover:text-bhx-600">Thống kê doanh thu</h3>
        <p class="text-sm text-gray-500 mt-2">Theo dõi doanh thu bán hàng theo ngày trong khoảng thời gian.</p>
        <div class="mt-4 text-bhx-600 font-medium text-sm">Xem báo cáo →</div>
    </a>

    <a href="{{ route('admin.baocao.donhang', ['tuNgay' => now()->subDays(30)->format('Y-m-d'), 'denNgay' => now()->format('Y-m-d')]) }}"
       class="bg-white rounded-xl shadow-md p-6 hover:shadow-xl transition group">
        <div class="text-4xl mb-3">📋</div>
        <h3 class="text-lg font-bold text-gray-800 group-hover:text-bhx-600">Thống kê đơn hàng</h3>
        <p class="text-sm text-gray-500 mt-2">Tổng hợp số lượng đơn hàng theo từng trạng thái.</p>
        <div class="mt-4 text-bhx-600 font-medium text-sm">Xem báo cáo →</div>
    </a>
</div>
@endsection
