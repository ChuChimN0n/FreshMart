@extends('layouts.management')
@section('title', 'Cảnh báo tồn kho')
@section('content')
@php
    $hasFilter = request()->filled('search') || request()->filled('trangThaiTon');
@endphp

<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('staff.kho.index') }}" class="hover:text-bhx-600 transition">Quản lý kho</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">Cảnh báo tồn kho</span>
</nav>

<x-page-header title="Cảnh báo tồn kho" :subtitle="'So sánh tồn kho với mức tồn tối thiểu từng sản phẩm'" />

<div class="grid grid-cols-2 gap-4 mb-5">
    <x-stat-card :url="route('staff.kho.alerts', ['trangThaiTon' => 'het'])" :number="$thongKe['hetHang']" label="Hết hàng" icon="bi-x-circle" color="red" :active="request('trangThaiTon') === 'het'" />
    <x-stat-card :url="route('staff.kho.alerts', ['trangThaiTon' => 'saphet'])" :number="$thongKe['sapHet']" label="Sắp hết hàng" icon="bi-exclamation-triangle" color="amber" :active="request('trangThaiTon') === 'saphet'" />
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-col md:flex-row md:flex-wrap gap-3 md:items-end">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-medium text-gray-500 mb-1" aria-hidden="true">&nbsp;</label>
            <x-toolbar-search name="search" placeholder="Tìm theo tên hoặc SKU sản phẩm..." />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1" for="trangThaiTon">Mức cảnh báo</label>
            <select name="trangThaiTon" id="trangThaiTon" onchange="this.form.submit()" class="bhx-input md:w-44">
                <option value="">Tất cả cảnh báo</option>
                <option value="het" @selected(request('trangThaiTon') === 'het')>Hết hàng</option>
                <option value="saphet" @selected(request('trangThaiTon') === 'saphet')>Sắp hết hàng</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bhx-btn-primary flex-1 md:flex-none">Tìm</button>
            @if($hasFilter)
                <a href="{{ route('staff.kho.alerts') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition" title="Xóa tất cả lọc">
                    <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[760px]">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-left font-semibold">Sản phẩm</th>
                    <th class="px-4 py-3 text-right font-semibold">Tồn kho</th>
                    <th class="px-4 py-3 text-right font-semibold">Ngưỡng tối thiểu</th>
                    <th class="px-4 py-3 text-left font-semibold">Cảnh báo</th>
                    @if(Auth::user()->canAccessRoute('staff.nhaphang.create'))
                        <th class="px-4 py-3 text-right font-semibold">Hành động</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sanPhams as $sp)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $sp->tenSP }}</p>
                            <p class="text-xs text-gray-400">{{ $sp->sku }}</p>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $sp->soLuong <= 0 ? 'text-red-600' : 'text-amber-600' }} whitespace-nowrap">
                            {{ $sp->soLuong }} {{ $sp->donVi }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500">{{ $sp->mucTonToiThieu }} {{ $sp->donVi }}</td>
                        <td class="px-4 py-3">
                            @if($sp->soLuong <= 0)
                                <span class="bhx-tag bg-red-100 text-red-700">Hết hàng</span>
                            @else
                                <span class="bhx-tag bg-amber-100 text-amber-700">Sắp hết hàng</span>
                            @endif
                        </td>
                        @if(Auth::user()->canAccessRoute('staff.nhaphang.create'))
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end">
                                    <a href="{{ route('staff.nhaphang.create', ['maNCC' => $sp->maNCC, 'maSP' => $sp->maSP]) }}" title="Nhập hàng cho sản phẩm này"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 text-green-700 px-3 py-2 text-sm font-medium hover:bg-green-100 transition whitespace-nowrap">
                                        <i class="bi bi-box-arrow-in-down"></i> Nhập hàng
                                    </a>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <x-empty-state icon="bi-bell" title="Không có cảnh báo tồn kho"
                                desc="Mọi sản phẩm đều còn hàng trên ngưỡng" />
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
