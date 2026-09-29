@extends('layouts.management')
@section('title', 'Nhập hàng')
@section('content')
@php
    $hasFilter = request()->filled('search') || request()->filled('trangThai') || request()->filled('tuNgay') || request()->filled('denNgay');
    $st = \App\Models\PhieuNhap::class;
@endphp

<x-page-header title="Nhập hàng"
    :subtitle="'Tổng '.$phieuNhaps->total().' phiếu nhập từ nhà cung cấp'"
    :action-url="route('staff.nhaphang.create')" action-label="Tạo phiếu nhập" action-icon="bi-plus-lg" />

<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-5">
    <x-stat-card :url="route('staff.nhaphang.index')" :number="$thongKe['tong']" label="Tổng phiếu" icon="bi-box-arrow-in-down" color="bhx" :active="!$hasFilter" />
    <x-stat-card :url="route('staff.nhaphang.index', ['trangThai' => $st::NHAP])" :number="$thongKe['choXacNhan']" label="Chờ xác nhận" icon="bi-hourglass-split" color="amber" :active="request('trangThai') === $st::NHAP" />
    <x-stat-card :url="route('staff.nhaphang.index', ['trangThai' => $st::DA_XAC_NHAN])" :number="$thongKe['daXacNhan']" label="Đã nhập kho" icon="bi-check-circle" color="green" :active="request('trangThai') === $st::DA_XAC_NHAN" />
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-col md:flex-row md:flex-wrap gap-3 md:items-end">
        <div class="flex-1 min-w-[240px]">
            <label class="block text-xs font-medium text-gray-500 mb-1" aria-hidden="true">&nbsp;</label>
            <x-toolbar-search name="search" placeholder="Tìm theo mã phiếu hoặc tên NCC..." />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1" for="trangThai">Trạng thái</label>
            <select name="trangThai" id="trangThai" onchange="this.form.submit()" class="bhx-input lg:w-44">
                <option value="">Mọi trạng thái</option>
                @foreach($st::TRANG_THAI as $key => $val)
                    <option value="{{ $key }}" @selected(request('trangThai') === $key)>{{ $val }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1" for="tuNgay">Từ ngày</label>
            <input type="date" name="tuNgay" id="tuNgay" value="{{ request('tuNgay') }}" class="bhx-input lg:w-44">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1" for="denNgay">Đến ngày</label>
            <input type="date" name="denNgay" id="denNgay" value="{{ request('denNgay') }}" class="bhx-input lg:w-44">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bhx-btn-primary flex-1 lg:flex-none">Tìm</button>
            @if($hasFilter)
                <a href="{{ route('staff.nhaphang.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition" title="Xóa tất cả lọc">
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
                    <th class="px-4 py-3 text-left font-semibold">Mã phiếu</th>
                    <th class="px-4 py-3 text-left font-semibold">Nhà cung cấp</th>
                    <th class="px-4 py-3 text-left font-semibold">Ngày tạo</th>
                    <th class="px-4 py-3 text-right font-semibold">Tổng tiền</th>
                    <th class="px-4 py-3 text-left font-semibold">Trạng thái</th>
                    <th class="px-4 py-3 text-right font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($phieuNhaps as $phieu)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3 font-semibold text-bhx-700 whitespace-nowrap">{{ $phieu->maPhieu }}</td>
                        <td class="px-4 py-3">{{ $phieu->nhaCungCap->tenNCC ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $phieu->ngayTao->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <span class="bhx-price">{{ number_format($phieu->tongTien, 0, ',', '.') }}đ</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="bhx-tag {{ $phieu->trangThaiBadge }}">{{ $phieu->trangThaiLabel }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @if($phieu->isNhap())
                                    <form method="POST" action="{{ route('staff.nhaphang.confirm', $phieu) }}" onsubmit="return confirm('Xác nhận nhập kho phiếu {{ $phieu->maPhieu }}? Tồn kho sẽ tăng và không thể hoàn tác.')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" title="Xác nhận nhập kho"
                                            class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 cursor-pointer flex items-center justify-center transition">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('staff.nhaphang.show', $phieu) }}" title="Xem chi tiết"
                                   class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-10 text-center text-gray-400">Chưa có phiếu nhập nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($phieuNhaps->hasPages())
        <div class="px-4 py-3 border-t">{{ $phieuNhaps->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
