@extends('layouts.app')
@section('title', 'Thống kê doanh thu')
@section('content')
<div class="mb-6">
    <a href="{{ route('admin.baocao.index') }}" class="text-bhx-600 hover:underline text-sm">← Quay lại báo cáo</a>
    <h1 class="text-2xl font-bold mt-2">Thống kê doanh thu</h1>
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

<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-sm text-gray-500">Tổng doanh thu</div>
        <div class="text-2xl font-bold text-bhx-600">{{ number_format($tongDoanhThu, 0, ',', '.') }}đ</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4 text-center">
        <div class="text-sm text-gray-500">Tổng đơn hàng</div>
        <div class="text-2xl font-bold text-blue-600">{{ number_format($tongSoDon) }}</div>
    </div>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-bhx-50">
            <tr>
                <th class="px-4 py-3 text-center">STT</th>
                <th class="px-4 py-3 text-left">Ngày</th>
                <th class="px-4 py-3 text-center">Số đơn hàng</th>
                <th class="px-4 py-3 text-right">Doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr class="border-t hover:bg-gray-50">
                <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                <td class="px-4 py-3 font-medium">{{ \Carbon\Carbon::parse($item->ngay)->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">{{ $item->soDon }}</span>
                </td>
                <td class="px-4 py-3 text-right font-medium text-bhx-600">{{ number_format($item->doanhThu, 0, ',', '.') }}đ</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                    <div class="text-3xl mb-2">💰</div>
                    Không có dữ liệu doanh thu trong khoảng thời gian này
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($data->count())
        <tfoot class="bg-bhx-50 font-bold">
            <tr>
                <td colspan="2" class="px-4 py-3 text-right">Tổng cộng</td>
                <td class="px-4 py-3 text-center">{{ number_format($tongSoDon) }}</td>
                <td class="px-4 py-3 text-right text-bhx-600">{{ number_format($tongDoanhThu, 0, ',', '.') }}đ</td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
