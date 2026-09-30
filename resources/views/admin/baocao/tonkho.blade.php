@extends('layouts.management')
@section('title', 'Báo cáo tồn kho')
@section('content')
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('admin.baocao.index') }}" class="hover:text-bhx-600 transition">Báo cáo</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">Tồn kho</span>
</nav>

<x-page-header title="Báo cáo tồn kho" subtitle="Tình trạng tồn kho hiện tại của kho chung"
    :action-url="route('admin.baocao.tonkho.export')" action-label="Xuất báo cáo" action-icon="bi-download" />

<div class="grid grid-cols-2 xl:grid-cols-3 gap-4 mb-5">
    <x-stat-card :number="number_format($conHang)" label="Còn hàng" icon="bi-check-circle" color="green" />
    <x-stat-card :number="number_format($sapHet)" label="Sắp hết hàng" icon="bi-exclamation-triangle" color="amber" />
    <x-stat-card :number="number_format($hetHang)" label="Hết hàng" icon="bi-x-circle" color="red" />
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[760px]">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-center font-semibold w-14">STT</th>
                    <th class="px-4 py-3 text-left font-semibold">Sản phẩm</th>
                    <th class="px-4 py-3 text-center font-semibold">Đơn vị</th>
                    <th class="px-4 py-3 text-right font-semibold">Số lượng tồn</th>
                    <th class="px-4 py-3 text-right font-semibold">Mức tối thiểu</th>
                    <th class="px-4 py-3 text-left font-semibold">Trạng thái tồn</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($data as $index => $item)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3 text-center text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item->tenSP }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="bhx-tag bg-gray-100 text-gray-600">{{ $item->donVi }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $item->trangThaiTon === 'HET_HANG' ? 'text-red-600' : ($item->trangThaiTon === 'SAP_HET_HANG' ? 'text-amber-600' : 'text-gray-800') }}">
                            {{ number_format($item->soLuongTon) }} {{ $item->donVi }}
                        </td>
                        <td class="px-4 py-3 text-right text-gray-500">{{ number_format($item->mucTonToiThieu) }}</td>
                        <td class="px-4 py-3">
                            @if($item->trangThaiTon === 'HET_HANG')
                                <span class="bhx-tag bg-red-100 text-red-700">Hết hàng</span>
                            @elseif($item->trangThaiTon === 'SAP_HET_HANG')
                                <span class="bhx-tag bg-amber-100 text-amber-700">Sắp hết hàng</span>
                            @else
                                <span class="bhx-tag bg-green-100 text-green-700">Còn hàng</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-empty-state icon="bi-boxes" title="Không có dữ liệu tồn kho" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
