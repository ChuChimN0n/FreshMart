@extends('layouts.management')
@section('title', 'Quản lý đơn hàng')
@section('content')
@php
    $st = \App\Models\DonHang::class;
    $hasFilter = request()->filled('search') || request()->filled('trangThai');
    $cnt = fn ($k) => $thongKe[$k] ?? 0;
@endphp
<x-page-header title="Quản lý đơn hàng"
    :subtitle="'Tổng '.array_sum($thongKe).' đơn hàng'" />

<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-5">
    <x-stat-card :url="route('staff.donhang.index')" :number="array_sum($thongKe)" label="Tất cả đơn" icon="bi-receipt" color="bhx" :active="!$hasFilter" />
    <x-stat-card :url="route('staff.donhang.index', ['trangThai' => $st::CHO_XAC_NHAN])" :number="$cnt($st::CHO_XAC_NHAN)" label="Chờ xác nhận" icon="bi-hourglass-split" color="amber" :active="request('trangThai') === $st::CHO_XAC_NHAN" />
    <x-stat-card :url="route('staff.donhang.index', ['trangThai' => $st::DANG_GIAO])" :number="$cnt($st::DANG_GIAO)" label="Đang giao" icon="bi-truck" color="blue" :active="request('trangThai') === $st::DANG_GIAO" />
    <x-stat-card :url="route('staff.donhang.index', ['trangThai' => $st::HOAN_THANH])" :number="$cnt($st::HOAN_THANH)" label="Hoàn thành" icon="bi-check-circle" color="green" :active="request('trangThai') === $st::HOAN_THANH" />
    <x-stat-card :url="route('staff.donhang.index', ['trangThai' => $st::DA_HUY])" :number="$cnt($st::DA_HUY)" label="Đã hủy" icon="bi-x-circle" color="red" :active="request('trangThai') === $st::DA_HUY" />
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-col lg:flex-row gap-3">
        <x-toolbar-search name="search" placeholder="Tìm theo mã đơn, tên người nhận, SĐT..." />
        <select name="trangThai" onchange="this.form.submit()" class="bhx-input lg:w-48">
            <option value="">Mọi trạng thái</option>
            @foreach(\App\Models\DonHang::TRANG_THAI as $key => $val)
                <option value="{{ $key }}" {{ request('trangThai') == $key ? 'selected' : '' }}>{{ $val }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button type="submit" class="bhx-btn-primary flex-1 lg:flex-none">Tìm</button>
            @if($hasFilter)
                <a href="{{ route('staff.donhang.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition" title="Xóa tất cả lọc">
                    <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[820px]">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-left font-semibold">Mã đơn</th>
                    <th class="px-4 py-3 text-left font-semibold">Khách hàng</th>
                    <th class="px-4 py-3 text-left font-semibold">Ngày đặt</th>
                    <th class="px-4 py-3 text-right font-semibold">Tổng tiền</th>
                    <th class="px-4 py-3 text-left font-semibold">Trạng thái</th>
                    <th class="px-4 py-3 text-right font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($donHangs as $dh)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3 font-semibold text-bhx-700 whitespace-nowrap">{{ $dh->maDon ?: '#'.$dh->maDH }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $dh->taiKhoan->hoTen ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $dh->tenNguoiNhan }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $dh->ngayDat->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <span class="bhx-price">{{ number_format($dh->tongTien, 0, ',', '.') }}đ</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bhx-tag {{ $dh->trangThaiBadge }}">{{ $dh->trangThaiLabel }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end">
                                <a href="{{ route('staff.donhang.detail', $dh) }}" title="Xem chi tiết"
                                   class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-empty-state icon="bi-receipt" title="Không có đơn hàng nào"
                                desc="Thử thay đổi từ khóa hoặc bộ lọc" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $donHangs->withQueryString()->links() }}</div>
@endsection
