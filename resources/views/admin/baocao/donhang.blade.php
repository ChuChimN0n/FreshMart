@extends('layouts.management')
@section('title', 'Thống kê đơn hàng')
@section('content')
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('admin.baocao.index') }}" class="hover:text-bhx-600 transition">Báo cáo</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">Theo đơn hàng</span>
</nav>

<x-page-header title="Thống kê đơn hàng"
    :subtitle="'Từ '.$tuNgay.' đến '.$denNgay" />

<x-report-filter :tuNgay="$tuNgay" :denNgay="$denNgay" :export-url="route('admin.baocao.donhang.export', request()->query())" />

@php $donHangBadges = \App\Models\DonHang::TRANG_THAI_BADGE; @endphp
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
    <x-stat-card :number="number_format($tongDon)" label="Tổng đơn hàng" icon="bi-receipt" color="blue" />
    <x-stat-card :number="number_format($tongTien, 0, ',', '.').'đ'" label="Tổng tiền" icon="bi-cash-stack" color="bhx" />
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[640px]">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-center font-semibold w-14">STT</th>
                    <th class="px-4 py-3 text-left font-semibold">Trạng thái</th>
                    <th class="px-4 py-3 text-center font-semibold">Số lượng đơn</th>
                    <th class="px-4 py-3 text-right font-semibold">Tổng tiền</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($data as $index => $item)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3 text-center text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-4 py-3">
                            <span class="bhx-tag {{ $donHangBadges[$item->trangThai] ?? 'bg-gray-100 text-gray-700' }}">{{ $item->label }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="bhx-tag bg-blue-50 text-blue-700">{{ $item->soLuong }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-bhx-700">{{ number_format($item->tongTien, 0, ',', '.') }}đ</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <x-empty-state icon="bi-receipt" title="Không có dữ liệu đơn hàng trong khoảng thời gian này" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($data->count())
                <tfoot class="bg-bhx-50/60 font-bold">
                    <tr>
                        <td colspan="2" class="px-4 py-3 text-right text-gray-700">Tổng cộng</td>
                        <td class="px-4 py-3 text-center text-blue-700">{{ number_format($tongDon) }}</td>
                        <td class="px-4 py-3 text-right text-bhx-700">{{ number_format($tongTien, 0, ',', '.') }}đ</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
