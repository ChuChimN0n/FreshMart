@extends('layouts.management')
@section('title', 'Lịch sử nhập - xuất kho')
@section('content')
@php
    $hasFilter = request()->filled('search') || request()->filled('loaiBienDong') || request()->filled('tuNgay') || request()->filled('denNgay');
@endphp

<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('staff.kho.index') }}" class="hover:text-bhx-600 transition">Quản lý kho</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">Lịch sử nhập - xuất kho</span>
</nav>

<x-page-header title="Lịch sử nhập - xuất kho" :subtitle="'Tổng '.$lichSus->total().' biến động kho'" />

<div class="bg-white rounded-xl shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-col md:flex-row md:flex-wrap gap-3 md:items-end">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-medium text-gray-500 mb-1" aria-hidden="true">&nbsp;</label>
            <x-toolbar-search name="search" placeholder="Tìm theo tên hoặc SKU sản phẩm..." />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1" for="loaiBienDong">Loại biến động</label>
            <select name="loaiBienDong" id="loaiBienDong" onchange="this.form.submit()" class="bhx-input md:w-44">
                <option value="">Mọi loại</option>
                @foreach($loaiLabels as $key => $val)
                    <option value="{{ $key }}" @selected(request('loaiBienDong') === $key)>{{ $val }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1" for="tuNgay">Từ ngày</label>
            <input type="date" name="tuNgay" id="tuNgay" value="{{ request('tuNgay') }}" class="bhx-input md:w-44">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1" for="denNgay">Đến ngày</label>
            <input type="date" name="denNgay" id="denNgay" value="{{ request('denNgay') }}" class="bhx-input md:w-44">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bhx-btn-primary flex-1 md:flex-none">Tìm</button>
            @if($hasFilter)
                <a href="{{ route('staff.kho.history') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition" title="Xóa tất cả lọc">
                    <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[900px]">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-left font-semibold">Thời gian</th>
                    <th class="px-4 py-3 text-left font-semibold">Sản phẩm</th>
                    <th class="px-4 py-3 text-left font-semibold">Loại</th>
                    <th class="px-4 py-3 text-right font-semibold">Số lượng</th>
                    <th class="px-4 py-3 text-right font-semibold">Tồn trước → sau</th>
                    <th class="px-4 py-3 text-left font-semibold">Tham chiếu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($lichSus as $ls)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $ls->thoiGian->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $ls->sanPham->tenSP ?? '#'.$ls->maSP }}</p>
                            <p class="text-xs text-gray-400">{{ $ls->sanPham->sku ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="bhx-tag {{ $loaiBadges[$ls->loaiBienDong] ?? 'bg-gray-100 text-gray-700' }}">{{ $loaiLabels[$ls->loaiBienDong] ?? $ls->loaiBienDong }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold whitespace-nowrap">
                            @if($ls->loaiBienDong === \App\Models\LichSuKho::BAN_HANG)
                                <span class="text-red-600">-{{ $ls->soLuong }}</span>
                            @else
                                <span class="text-green-600">+{{ $ls->soLuong }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500 whitespace-nowrap">{{ $ls->tonTruoc }} → {{ $ls->tonSau }}</td>
                        <td class="px-4 py-3 text-gray-500">
                            @if($ls->maPN)
                                PN #{{ $ls->maPN }}
                            @elseif($ls->maDH)
                                ĐH #{{ $ls->maDH }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-empty-state icon="bi-clock-history" title="Chưa phát sinh biến động kho"
                                desc="Thử thay đổi từ khóa hoặc bộ lọc" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($lichSus->hasPages())
        <div class="px-4 py-3 border-t">{{ $lichSus->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
