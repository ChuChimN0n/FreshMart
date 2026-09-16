@extends('layouts.app')
@section('title', 'Quản lý đánh giá')
@section('content')
<h1 class="text-2xl font-bold mb-4">Quản lý đánh giá</h1>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên sản phẩm..." class="border rounded px-3 py-2 flex-1">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tìm</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left">Khách hàng</th>
                <th class="px-4 py-3 text-left">Sản phẩm</th>
                <th class="px-4 py-3 text-center">Số sao</th>
                <th class="px-4 py-3 text-left">Nội dung</th>
            </tr>
        </thead>
        <tbody>
            @forelse($danhGias as $dg)
            <tr class="border-t">
                <td class="px-4 py-3">{{ $dg->taiKhoan->hoTen ?? '' }}</td>
                <td class="px-4 py-3">{{ $dg->sanPham->tenSP ?? '' }}</td>
                <td class="px-4 py-3 text-center text-yellow-500">{{ str_repeat('⭐', $dg->soSao) }}</td>
                <td class="px-4 py-3 max-w-xs truncate">{{ $dg->noiDung }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Không có đánh giá nào</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $danhGias->withQueryString()->links() }}</div>
@endsection
