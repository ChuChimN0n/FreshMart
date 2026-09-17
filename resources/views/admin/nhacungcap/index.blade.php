@extends('layouts.app')
@section('title', 'Quản lý nhà cung cấp')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Quản lý nhà cung cấp</h1>
    <a href="{{ route('admin.nhacungcap.create') }}" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">+ Thêm NCC</a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm NCC..." class="border rounded px-3 py-2 flex-1">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tìm</button>
</form>

<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Mã NCC</th>
                <th class="px-4 py-2 text-left">Tên NCC</th>
                <th class="px-4 py-2 text-left">Số ĐT</th>
                <th class="px-4 py-2 text-left">Email</th>
                <th class="px-4 py-2 text-left">Địa chỉ</th>
                <th class="px-4 py-2 text-left">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($nhaCungCaps as $ncc)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $ncc->maNCC }}</td>
                <td class="px-4 py-2">{{ $ncc->tenNCC }}</td>
                <td class="px-4 py-2">{{ $ncc->soDienThoai }}</td>
                <td class="px-4 py-2">{{ $ncc->email }}</td>
                <td class="px-4 py-2">{{ $ncc->diaChi }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('admin.nhacungcap.edit', $ncc) }}" class="text-blue-600 hover:underline">Sửa</a>
                    <form method="POST" action="{{ route('admin.nhacungcap.destroy', $ncc) }}" class="inline" data-confirm="Xóa nhà cung cấp {{ $ncc->tenNCC }}?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline ml-2">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-4 text-center text-gray-500">Không có NCC nào</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $nhaCungCaps->withQueryString()->links() }}</div>
@endsection
