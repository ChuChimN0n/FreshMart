@extends('layouts.management')
@section('title', 'Báo cáo nhập hàng')
@section('content')
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('admin.baocao.index') }}" class="hover:text-bhx-600 transition">Báo cáo</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">Nhập hàng</span>
</nav>

<x-page-header title="Báo cáo nhập hàng"
    :subtitle="'Từ '.$tuNgay.' đến '.$denNgay" />

<x-report-filter :tuNgay="$tuNgay" :denNgay="$denNgay" :export-url="route('admin.baocao.nhaphang.export', request()->query())" />

<div class="grid grid-cols-2 xl:grid-cols-3 gap-4 mb-5">
    <x-stat-card :number="number_format($tongPhieu)" label="Phiếu đã xác nhận" icon="bi-box-arrow-in-down" color="green" />
    <x-stat-card :number="number_format($tongSL)" label="Tổng số lượng nhập" icon="bi-box-seam" color="blue" />
    <x-stat-card :number="number_format($tongTien, 0, ',', '.').'đ'" label="Tổng giá trị nhập" icon="bi-cash-stack" color="bhx" />
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden mb-5">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[860px]">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-center font-semibold w-14">STT</th>
                    <th class="px-4 py-3 text-left font-semibold">Mã phiếu</th>
                    <th class="px-4 py-3 text-left font-semibold">Nhà cung cấp</th>
                    <th class="px-4 py-3 text-left font-semibold">Sản phẩm</th>
                    <th class="px-4 py-3 text-center font-semibold">Số lượng</th>
                    <th class="px-4 py-3 text-right font-semibold">Giá nhập</th>
                    <th class="px-4 py-3 text-right font-semibold">Thành tiền</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($data as $index => $item)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3 text-center text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-semibold text-bhx-700 whitespace-nowrap">{{ $item->maPhieu }}</td>
                        <td class="px-4 py-3">{{ $item->tenNCC }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $item->tenSP }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="bhx-tag bg-green-50 text-green-700">{{ number_format($item->soLuong) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($item->giaNhap, 0, ',', '.') }}đ</td>
                        <td class="px-4 py-3 text-right font-semibold text-bhx-700">{{ number_format($item->thanhTien, 0, ',', '.') }}đ</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <x-empty-state icon="bi-box-arrow-in-down" title="Không có dữ liệu nhập hàng" desc="Thử thay đổi khoảng thời gian" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($data->count())
                <tfoot class="bg-bhx-50/60 font-bold">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right text-gray-700">Tổng cộng ({{ $tongSL }} sản phẩm)</td>
                        <td colspan="3" class="px-4 py-3 text-right text-bhx-700">{{ number_format($tongTien, 0, ',', '.') }}đ</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

@if($theoNCC->count())
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <h2 class="font-bold text-gray-800 px-4 pt-4">Tổng hợp theo nhà cung cấp</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[640px]">
                <thead class="bg-gray-50">
                    <tr class="text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3 text-left font-semibold">Nhà cung cấp</th>
                        <th class="px-4 py-3 text-center font-semibold">Số phiếu</th>
                        <th class="px-4 py-3 text-center font-semibold">Tổng số lượng</th>
                        <th class="px-4 py-3 text-right font-semibold">Tổng tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($theoNCC as $ncc)
                        <tr class="hover:bg-bhx-50/50 transition">
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $ncc['tenNCC'] }}</td>
                            <td class="px-4 py-3 text-center">{{ $ncc['soPhieu'] }}</td>
                            <td class="px-4 py-3 text-center">{{ number_format($ncc['tongSL']) }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-bhx-700">{{ number_format($ncc['tongTien'], 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
