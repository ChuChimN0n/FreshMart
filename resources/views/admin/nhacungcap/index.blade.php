@extends('layouts.management')
@section('title', 'Quản lý nhà cung cấp')
@section('content')
<x-page-header title="Quản lý nhà cung cấp"
    :subtitle="'Tổng '.$nhaCungCaps->total().' nhà cung cấp'"
    :actionUrl="route('admin.nhacungcap.create')" actionLabel="Thêm NCC" />

<div class="bg-white rounded-xl shadow-sm p-4 mb-5">
    <form method="GET" class="flex gap-2">
        <x-toolbar-search name="search" placeholder="Tìm theo mã NCC, tên, SĐT, email..." />
        <button type="submit" class="bhx-btn-primary">Tìm</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[760px]">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-left font-semibold">Nhà cung cấp</th>
                    <th class="px-4 py-3 text-left font-semibold">Liên hệ</th>
                    <th class="px-4 py-3 text-center font-semibold">Sản phẩm</th>
                    <th class="px-4 py-3 text-right font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($nhaCungCaps as $ncc)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-lg bg-orange-50 text-bhx-orange flex items-center justify-center text-lg font-bold shrink-0">
                                    {{ mb_substr($ncc->tenNCC, 0, 1) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800">{{ $ncc->tenNCC }}</p>
                                    <p class="text-xs text-gray-400"><span class="font-mono font-semibold text-gray-500">{{ $ncc->codeNCC ?: '—' }}</span> · #{{ $ncc->maNCC }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <p class="flex items-center gap-1.5"><i class="bi bi-telephone text-gray-400"></i> {{ $ncc->soDienThoai }}</p>
                            <p class="flex items-center gap-1.5 mt-1"><i class="bi bi-envelope text-gray-400"></i> {{ $ncc->email ?: '—' }}</p>
                            <p class="flex items-center gap-1.5 mt-1 text-xs text-gray-400"><i class="bi bi-geo-alt"></i> {{ $ncc->diaChi ?: '—' }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('staff.sanpham.index', ['maNCC' => $ncc->maNCC]) }}" title="Xem sản phẩm của nhà cung cấp này"
                               class="bhx-tag bg-blue-50 text-blue-700 hover:bg-blue-100 hover:underline transition">
                                {{ $ncc->san_phams_count }} sản phẩm <i class="bi bi-arrow-right text-[10px]"></i>
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('staff.sanpham.index', ['maNCC' => $ncc->maNCC]) }}" title="Xem sản phẩm"
                                   class="w-8 h-8 rounded-lg bg-bhx-50 text-bhx-600 hover:bg-bhx-100 flex items-center justify-center transition">
                                    <i class="bi bi-box-seam"></i>
                                </a>
                                <a href="{{ route('admin.nhacungcap.edit', $ncc) }}" title="Sửa nhà cung cấp"
                                   class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.nhacungcap.destroy', $ncc) }}" class="inline" data-confirm="Xóa nhà cung cấp {{ $ncc->tenNCC }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Xóa nhà cung cấp"
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <x-empty-state icon="bi-truck" title="Không tìm thấy nhà cung cấp nào"
                                desc="Thử thay đổi từ khóa tìm kiếm" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $nhaCungCaps->withQueryString()->links() }}</div>
@endsection
