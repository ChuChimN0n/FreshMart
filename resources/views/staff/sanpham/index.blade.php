@extends('layouts.app')
@section('title', 'Quản lý sản phẩm')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Quản lý sản phẩm</h1>
    <a href="{{ route('staff.sanpham.create') }}" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">+ Thêm sản phẩm</a>
</div>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm sản phẩm..." class="border rounded px-3 py-2 flex-1">
    <select name="maDM" class="border rounded px-3 py-2">
        <option value="">-- Tất cả danh mục --</option>
        @foreach($danhMucs as $dm)
        <option value="{{ $dm->maDM }}" {{ request('maDM') == $dm->maDM ? 'selected' : '' }}>{{ $dm->tenDM }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tìm</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Mã SP</th>
                <th class="px-4 py-2 text-left">Tên sản phẩm</th>
                <th class="px-4 py-2 text-left">Danh mục</th>
                <th class="px-4 py-2 text-left">NCC</th>
                <th class="px-4 py-2 text-right">Giá</th>
                <th class="px-4 py-2 text-right">Tồn kho</th>
                <th class="px-4 py-2 text-left">Trạng thái</th>
                <th class="px-4 py-2 text-left">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sanPhams as $sp)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $sp->maSP }}</td>
                <td class="px-4 py-2 font-medium">{{ $sp->tenSP }}</td>
                <td class="px-4 py-2">{{ $sp->danhMuc->tenDM ?? '' }}</td>
                <td class="px-4 py-2">{{ $sp->nhaCungCap->tenNCC ?? '' }}</td>
                <td class="px-4 py-2 text-right">{{ number_format($sp->giaBan, 0, ',', '.') }}đ</td>
                <td class="px-4 py-2 text-right">{{ $sp->soLuong }} {{ $sp->donVi }}</td>
                <td class="px-4 py-2">
                    @if($sp->trangThai == \App\Models\SanPham::DANG_BAN)
                        <span class="text-bhx-600">Đang bán</span>
                    @else
                        <span class="text-red-600">Ngừng bán</span>
                    @endif
                </td>
                <td class="px-4 py-2">
                    <a href="{{ route('staff.sanpham.edit', $sp) }}" class="text-blue-600 hover:underline">Sửa</a>
                    <form method="POST" action="{{ route('staff.sanpham.destroy', $sp) }}" class="inline" data-confirm="Xóa sản phẩm {{ $sp->tenSP }}?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline ml-2">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-4 py-4 text-center text-gray-500">Không có sản phẩm nào</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $sanPhams->withQueryString()->links() }}</div>
@endsection
