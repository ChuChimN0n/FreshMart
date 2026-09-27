@extends('layouts.management')
@section('title', 'Quản lý đánh giá')
@section('content')
@php $hasFilter = request()->filled('search') || request()->filled('soSao'); @endphp
<x-page-header title="Quản lý đánh giá"
    :subtitle="'Tổng '.$danhGias->total().' lượt đánh giá'" />

<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
    <x-stat-card :url="route('staff.danhgia.index')" :number="$thongKe['tong']" label="Tổng đánh giá" icon="bi-star" color="bhx" :active="!$hasFilter" />
    <x-stat-card :number="$thongKe['trungBinh'].'/5'" label="Điểm trung bình" icon="bi-star-half" color="amber" />
    <x-stat-card :url="route('staff.danhgia.index', ['soSao' => 5])" :number="$thongKe['namSao']" label="Đánh giá 5 sao" icon="bi-emoji-smile" color="green" :active="request('soSao') == 5" />
    <x-stat-card :url="route('staff.danhgia.index', ['soSao' => 2])" :number="$thongKe['thapSao']" label="Từ 2 sao trở xuống" icon="bi-emoji-frown" color="red" />
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-col lg:flex-row gap-3">
        <x-toolbar-search name="search" placeholder="Tìm theo tên sản phẩm..." />
        <select name="soSao" onchange="this.form.submit()" class="bhx-input lg:w-44">
            <option value="">Mọi số sao</option>
            @for($s = 5; $s >= 1; $s--)
                <option value="{{ $s }}" {{ request('soSao') == $s ? 'selected' : '' }}>{{ $s }} sao</option>
            @endfor
        </select>
        <div class="flex gap-2">
            <button type="submit" class="bhx-btn-primary flex-1 lg:flex-none">Tìm</button>
            @if($hasFilter)
                <a href="{{ route('staff.danhgia.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition" title="Xóa tất cả lọc">
                    <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[720px]">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-left font-semibold">Khách hàng</th>
                    <th class="px-4 py-3 text-left font-semibold">Sản phẩm</th>
                    <th class="px-4 py-3 text-left font-semibold">Đánh giá</th>
                    <th class="px-4 py-3 text-left font-semibold">Nội dung</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($danhGias as $dg)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-full bg-bhx-100 text-bhx-700 flex items-center justify-center text-sm font-bold shrink-0">
                                    {{ mb_strtoupper(mb_substr($dg->taiKhoan->hoTen ?? '?', 0, 1)) }}
                                </span>
                                <span class="font-medium text-gray-800">{{ $dg->taiKhoan->hoTen ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $dg->sanPham->tenSP ?? 'SP đã xóa' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-amber-400 tracking-tight">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi {{ $i <= $dg->soSao ? 'bi-star-fill' : 'bi-star text-gray-300' }}"></i>
                                @endfor
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 max-w-xs">
                            <p class="line-clamp-2" title="{{ $dg->noiDung }}">{{ $dg->noiDung }}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <x-empty-state icon="bi-star" title="Không có đánh giá nào"
                                desc="Thử thay đổi từ khóa hoặc bộ lọc" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $danhGias->withQueryString()->links() }}</div>
@endsection
