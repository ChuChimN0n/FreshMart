@extends('layouts.management')
@section('title', 'Quản lý kho')
@section('content')
@php
    $hasFilter = request()->filled('search');
@endphp

<x-page-header title="Quản lý kho" :subtitle="'Tổng '.$thongKe['tong'].' sản phẩm trong kho chung'" />

<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-5">
    <x-stat-card :url="route('staff.kho.index')" :number="$thongKe['tong']" label="Tổng sản phẩm" icon="bi-boxes" color="bhx" :active="!$hasFilter" />
    <x-stat-card :url="route('staff.kho.alerts', ['trangThaiTon' => 'saphet'])" :number="$thongKe['sapHet']" label="Sắp hết hàng" icon="bi-exclamation-triangle" color="amber" />
    <x-stat-card :url="route('staff.kho.alerts', ['trangThaiTon' => 'het'])" :number="$thongKe['hetHang']" label="Hết hàng" icon="bi-x-circle" color="red" />
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-col md:flex-row md:flex-wrap gap-3 md:items-end">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-medium text-gray-500 mb-1" aria-hidden="true">&nbsp;</label>
            <x-toolbar-search name="search" placeholder="Tìm theo tên hoặc SKU sản phẩm..." />
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bhx-btn-primary flex-1 md:flex-none">Tìm</button>
            @if($hasFilter)
                <a href="{{ route('staff.kho.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition" title="Xóa tất cả lọc">
                    <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-5 flex flex-wrap gap-2 text-sm">
    <a href="{{ route('staff.kho.history') }}" class="inline-flex items-center gap-1.5 text-bhx-600 hover:text-bhx-700 font-medium">
        <i class="bi bi-clock-history"></i> Lịch sử nhập - xuất kho
    </a>
    <span class="text-gray-300">|</span>
    <a href="{{ route('staff.kho.alerts') }}" class="inline-flex items-center gap-1.5 text-bhx-600 hover:text-bhx-700 font-medium">
        <i class="bi bi-bell"></i> Cảnh báo tồn kho
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[820px]">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-left font-semibold">Sản phẩm</th>
                    <th class="px-4 py-3 text-left font-semibold">Danh mục</th>
                    <th class="px-4 py-3 text-right font-semibold">Tồn kho</th>
                    <th class="px-4 py-3 text-right font-semibold">Ngưỡng tối thiểu</th>
                    <th class="px-4 py-3 text-left font-semibold">Trạng thái tồn</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sanPhams as $sp)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $sp->tenSP }}</p>
                            <p class="text-xs text-gray-400">{{ $sp->sku }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bhx-tag bg-gray-100 text-gray-600">{{ $sp->danhMuc->tenDM ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <span class="font-semibold {{ $sp->soLuong <= 0 ? 'text-red-600' : ($sp->isLowStock() ? 'text-amber-600' : 'text-gray-800') }}">
                                {{ $sp->soLuong }} {{ $sp->donVi }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500">{{ $sp->mucTonToiThieu }} {{ $sp->donVi }}</td>
                        <td class="px-4 py-3">
                            @if($sp->soLuong <= 0)
                                <span class="bhx-tag bg-red-100 text-red-700">Hết hàng</span>
                            @elseif($sp->isLowStock())
                                <span class="bhx-tag bg-amber-100 text-amber-700">Sắp hết hàng</span>
                            @else
                                <span class="bhx-tag bg-green-100 text-green-700">Còn hàng</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <x-empty-state icon="bi-boxes" title="Không có dữ liệu tồn kho"
                                desc="Thử thay đổi từ khóa tìm kiếm" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($sanPhams->hasPages())
        <div class="px-4 py-3 border-t">{{ $sanPhams->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
