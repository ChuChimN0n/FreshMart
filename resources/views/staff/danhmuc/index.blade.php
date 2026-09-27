@extends('layouts.management')
@section('title', 'Quản lý danh mục')
@section('content')
<x-page-header title="Quản lý danh mục"
    :subtitle="'Tổng '.$danhMucs->total().' danh mục'"
    :actionUrl="route('staff.danhmuc.create')" actionLabel="Thêm danh mục" />

<div class="bg-white rounded-xl shadow-sm p-4 mb-5">
    <form method="GET" class="flex gap-2">
        <x-toolbar-search name="search" placeholder="Tìm theo tên danh mục..." />
        <button type="submit" class="bhx-btn-primary">Tìm</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[560px]">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-left font-semibold">Danh mục</th>
                    <th class="px-4 py-3 text-center font-semibold">Số sản phẩm</th>
                    <th class="px-4 py-3 text-right font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($danhMucs as $dm)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-lg bg-bhx-50 text-bhx-600 flex items-center justify-center text-lg shrink-0">
                                    <i class="bi bi-tags"></i>
                                </span>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800">{{ $dm->tenDM }}</p>
                                    <p class="text-xs text-gray-400">Mã #{{ $dm->maDM }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('staff.sanpham.index', ['maDM' => $dm->maDM]) }}" title="Xem sản phẩm thuộc danh mục này"
                               class="bhx-tag bg-blue-50 text-blue-700 hover:bg-blue-100 hover:underline transition">
                                {{ $dm->san_phams_count }} sản phẩm <i class="bi bi-arrow-right text-[10px]"></i>
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('staff.sanpham.index', ['maDM' => $dm->maDM]) }}" title="Xem sản phẩm"
                                   class="w-8 h-8 rounded-lg bg-bhx-50 text-bhx-600 hover:bg-bhx-100 flex items-center justify-center transition">
                                    <i class="bi bi-box-seam"></i>
                                </a>
                                <a href="{{ route('staff.danhmuc.edit', $dm) }}" title="Sửa danh mục"
                                   class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('staff.danhmuc.destroy', $dm) }}" class="inline" data-confirm="Xóa danh mục {{ $dm->tenDM }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Xóa danh mục"
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">
                            <x-empty-state icon="bi-tags" title="Không tìm thấy danh mục nào"
                                desc="Thử thay đổi từ khóa tìm kiếm" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $danhMucs->withQueryString()->links() }}</div>
@endsection
