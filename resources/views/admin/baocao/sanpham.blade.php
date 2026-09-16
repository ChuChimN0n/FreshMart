@extends('layouts.app')
@section('title', 'Thống kê sản phẩm')
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.baocao.index') }}" class="text-bhx-600 hover:underline text-sm">← Quay lại báo cáo</a>
    <h1 class="text-2xl font-bold mt-2">Thống kê sản phẩm</h1>
</div>

<div class="bg-white rounded-lg shadow p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
            <input type="date" name="tuNgay" value="{{ $tuNgay }}" class="border rounded px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
            <input type="date" name="denNgay" value="{{ $denNgay }}" class="border rounded px-3 py-2" required>
        </div>
        <button type="submit" class="bg-bhx-500 text-white px-6 py-2 rounded hover:bg-bhx-600 font-medium">Thống kê</button>
    </form>
</div>

@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ $errors->first() }}
    </div>
@endif

<div class="grid grid-cols-2 md:grid-cols-{{ $tongBanTheoDonVi->count() + 1 }} gap-4 mb-6">
    @foreach($tongBanTheoDonVi as $donVi => $soLuong)
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-sm text-gray-500">Tổng bán ({{ $donVi }})</div>
        <div class="text-2xl font-bold text-blue-600">{{ number_format($soLuong) }}</div>
    </div>
    @endforeach
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-sm text-gray-500">Tổng tiền</div>
        <div class="text-2xl font-bold text-bhx-600">{{ number_format($tongTien, 0, ',', '.') }}đ</div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-bhx-50">
            <tr>
                <th class="px-4 py-3 text-center">STT</th>
                <th class="px-4 py-3 text-left">Tên sản phẩm</th>
                <th class="px-4 py-3 text-center">Đơn vị</th>
                <th class="px-4 py-3 text-right">Đơn giá</th>
                <th class="px-4 py-3 text-center">Số lượng bán</th>
                <th class="px-4 py-3 text-right">Tổng tiền</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                <td class="px-4 py-3 font-medium">{{ $item->tenSP }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-medium">{{ $item->donVi }}</span>
                </td>
                <td class="px-4 py-3 text-right">{{ number_format($item->giaBan, 0, ',', '.') }}đ</td>
                <td class="px-4 py-3 text-center">
                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">{{ number_format($item->tongBan) }} {{ $item->donVi }}</span>
                </td>
                <td class="px-4 py-3 text-right font-medium text-bhx-600">{{ number_format($item->tongTien, 0, ',', '.') }}đ</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                    <div class="text-3xl mb-2">📊</div>
                    Không có dữ liệu thống kê trong khoảng thời gian này
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($data->count())
        <tfoot class="bg-bhx-50 font-bold">
            <tr>
                <td colspan="4" class="px-4 py-3 text-right">Tổng cộng</td>
                <td class="px-4 py-3 text-center text-bhx-600">
                    @foreach($tongBanTheoDonVi as $donVi => $soLuong)
                        {{ number_format($soLuong) }} {{ $donVi }}@if(!$loop->last), @endif
                    @endforeach
                </td>
                <td class="px-4 py-3 text-right text-bhx-600">{{ number_format($tongTien, 0, ',', '.') }}đ</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
