@extends('layouts.management')
@section('title', 'Báo cáo thống kê')
@section('content')
<x-page-header title="Báo cáo thống kê" subtitle="Chọn loại báo cáo bạn muốn xem" />

<div class="grid grid-cols-1 md:grid-cols-3 gap-5">
    @if(Auth::user()->canAccessRoute('admin.baocao.sanpham'))
        <a href="{{ route('admin.baocao.sanpham', ['tuNgay' => now()->subDays(30)->format('Y-m-d'), 'denNgay' => now()->format('Y-m-d')]) }}"
           class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition group">
            <span class="w-12 h-12 rounded-xl bg-bhx-50 text-bhx-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-105 transition"><i class="bi bi-box-seam"></i></span>
            <h3 class="text-lg font-bold text-gray-800 group-hover:text-bhx-600 transition">Thống kê sản phẩm</h3>
            <p class="text-sm text-gray-500 mt-2">Số lượng đã bán, sản phẩm bán chạy trong khoảng thời gian.</p>
            <div class="mt-4 text-bhx-600 font-medium text-sm">Xem báo cáo <i class="bi bi-arrow-right"></i></div>
        </a>
    @endif

    @if(Auth::user()->canAccessRoute('admin.baocao.doanhthu'))
        <a href="{{ route('admin.baocao.doanhthu', ['tuNgay' => now()->subDays(30)->format('Y-m-d'), 'denNgay' => now()->format('Y-m-d')]) }}"
           class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition group">
            <span class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-105 transition"><i class="bi bi-cash-stack"></i></span>
            <h3 class="text-lg font-bold text-gray-800 group-hover:text-bhx-600 transition">Thống kê doanh thu</h3>
            <p class="text-sm text-gray-500 mt-2">Theo dõi doanh thu bán hàng theo ngày trong khoảng thời gian.</p>
            <div class="mt-4 text-bhx-600 font-medium text-sm">Xem báo cáo <i class="bi bi-arrow-right"></i></div>
        </a>
    @endif

    @if(Auth::user()->canAccessRoute('admin.baocao.donhang'))
        <a href="{{ route('admin.baocao.donhang', ['tuNgay' => now()->subDays(30)->format('Y-m-d'), 'denNgay' => now()->format('Y-m-d')]) }}"
           class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition group">
            <span class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-105 transition"><i class="bi bi-receipt"></i></span>
            <h3 class="text-lg font-bold text-gray-800 group-hover:text-bhx-600 transition">Thống kê đơn hàng</h3>
            <p class="text-sm text-gray-500 mt-2">Tổng hợp số lượng đơn hàng theo từng trạng thái.</p>
            <div class="mt-4 text-bhx-600 font-medium text-sm">Xem báo cáo <i class="bi bi-arrow-right"></i></div>
        </a>
    @endif
</div>
@endsection
