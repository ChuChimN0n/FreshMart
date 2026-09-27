@extends('layouts.management')
@section('title', 'Quản lý sản phẩm')
@section('content')
@php
    $hasFilter = request()->filled('search') || request()->filled('maDM') || request()->filled('maNCC') || request()->filled('trangThai') || request()->filled('tonKho') || request()->filled('sort');
@endphp

{{-- ===== Header ===== --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Quản lý sản phẩm</h1>
        <p class="text-sm text-gray-500 mt-1">Tổng {{ $sanPhams->total() }} sản phẩm trong kho hàng</p>
    </div>
    <a href="{{ route('staff.sanpham.create') }}" class="bhx-btn-primary">
        <i class="bi bi-plus-lg"></i> Thêm sản phẩm
    </a>
</div>

{{-- ===== Stat cards (click để lọc nhanh) ===== --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
    <a href="{{ route('staff.sanpham.index') }}" class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition {{ !$hasFilter ? 'ring-2 ring-bhx-500' : '' }}">
        <span class="w-11 h-11 rounded-full bg-bhx-50 text-bhx-600 flex items-center justify-center text-xl shrink-0"><i class="bi bi-box-seam"></i></span>
        <span>
            <span class="block text-2xl font-bold text-gray-800 leading-none">{{ $thongKe['tong'] }}</span>
            <span class="block text-xs text-gray-500 mt-1">Tổng sản phẩm</span>
        </span>
    </a>
    <a href="{{ route('staff.sanpham.index', ['trangThai' => \App\Models\SanPham::DANG_BAN]) }}" class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition {{ request('trangThai') === \App\Models\SanPham::DANG_BAN ? 'ring-2 ring-bhx-500' : '' }}">
        <span class="w-11 h-11 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-xl shrink-0"><i class="bi bi-check-circle"></i></span>
        <span>
            <span class="block text-2xl font-bold text-gray-800 leading-none">{{ $thongKe['dangBan'] }}</span>
            <span class="block text-xs text-gray-500 mt-1">Đang bán</span>
        </span>
    </a>
    <a href="{{ route('staff.sanpham.index', ['tonKho' => 'saphet']) }}" class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition {{ request('tonKho') === 'saphet' ? 'ring-2 ring-amber-500' : '' }}">
        <span class="w-11 h-11 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0"><i class="bi bi-exclamation-triangle"></i></span>
        <span>
            <span class="block text-2xl font-bold text-gray-800 leading-none">{{ $thongKe['sapHet'] }}</span>
            <span class="block text-xs text-gray-500 mt-1">Sắp hết hàng (≤ 5)</span>
        </span>
    </a>
    <a href="{{ route('staff.sanpham.index', ['tonKho' => 'het']) }}" class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition {{ request('tonKho') === 'het' ? 'ring-2 ring-red-500' : '' }}">
        <span class="w-11 h-11 rounded-full bg-red-50 text-red-600 flex items-center justify-center text-xl shrink-0"><i class="bi bi-x-circle"></i></span>
        <span>
            <span class="block text-2xl font-bold text-gray-800 leading-none">{{ $thongKe['hetHang'] }}</span>
            <span class="block text-xs text-gray-500 mt-1">Hết hàng</span>
        </span>
    </a>
</div>

{{-- ===== Toolbar lọc ===== --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-5">
    <form method="GET" id="filterForm" class="space-y-3">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1 min-w-0">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm theo tên, mã SKU hoặc mã SP..."
                       class="bhx-input pl-9 pr-9">
                @if(request()->filled('search'))
                    <a href="{{ route('staff.sanpham.index', request()->except('search', 'page')) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" title="Xóa tìm kiếm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
            <div class="flex gap-2 shrink-0">
                <button type="submit" class="bhx-btn-primary flex-1 md:flex-none">Tìm</button>
                @if($hasFilter)
                    <a href="{{ route('staff.sanpham.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition" title="Xóa tất cả lọc">
                        <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                    </a>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3">
            <div>
                <x-searchable-select name="maDM" :autosubmit="true"
                    placeholder="Tất cả danh mục" searchPlaceholder="Gõ để tìm danh mục..."
                    :options="$danhMucs->map(fn ($dm) => ['value' => $dm->maDM, 'label' => $dm->tenDM])->prepend(['value' => '', 'label' => 'Tất cả danh mục'])->values()->all()"
                    :selected="request('maDM')" />
            </div>
            <div>
                <x-searchable-select name="maNCC" :autosubmit="true"
                    placeholder="Mọi nhà cung cấp" searchPlaceholder="Gõ để tìm NCC..."
                    :options="$nhaCungCaps->map(fn ($ncc) => ['value' => $ncc->maNCC, 'label' => ($ncc->codeNCC ? $ncc->codeNCC.' · ' : '').$ncc->tenNCC])->prepend(['value' => '', 'label' => 'Mọi nhà cung cấp'])->values()->all()"
                    :selected="request('maNCC')" />
            </div>
            <select name="trangThai" onchange="document.getElementById('filterForm').submit()" class="bhx-input">
                <option value="">Mọi trạng thái</option>
                <option value="{{ \App\Models\SanPham::DANG_BAN }}" {{ request('trangThai') === \App\Models\SanPham::DANG_BAN ? 'selected' : '' }}>Đang bán</option>
                <option value="{{ \App\Models\SanPham::NGUNG_BAN }}" {{ request('trangThai') === \App\Models\SanPham::NGUNG_BAN ? 'selected' : '' }}>Ngừng bán</option>
            </select>
            <select name="tonKho" onchange="document.getElementById('filterForm').submit()" class="bhx-input">
                <option value="">Mọi tồn kho</option>
                <option value="con" {{ request('tonKho') === 'con' ? 'selected' : '' }}>Còn hàng</option>
                <option value="saphet" {{ request('tonKho') === 'saphet' ? 'selected' : '' }}>Sắp hết</option>
                <option value="het" {{ request('tonKho') === 'het' ? 'selected' : '' }}>Hết hàng</option>
            </select>
            <select name="sort" onchange="document.getElementById('filterForm').submit()" class="bhx-input">
                <option value="">Mới nhất</option>
                <option value="ten_asc" {{ request('sort') === 'ten_asc' ? 'selected' : '' }}>Tên A–Z</option>
                <option value="gia_asc" {{ request('sort') === 'gia_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                <option value="gia_desc" {{ request('sort') === 'gia_desc' ? 'selected' : '' }}>Giá giảm dần</option>
            </select>
        </div>
    </form>
</div>

{{-- ===== Bảng sản phẩm ===== --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[880px]">
            <thead class="bg-gray-50 sticky top-0">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-left font-semibold w-20">Mã</th>
                    <th class="px-4 py-3 text-left font-semibold">Sản phẩm</th>
                    <th class="px-4 py-3 text-left font-semibold">Danh mục</th>
                    <th class="px-4 py-3 text-left font-semibold">Nhà cung cấp</th>
                    <th class="px-4 py-3 text-right font-semibold">Giá bán</th>
                    <th class="px-4 py-3 text-right font-semibold">Tồn kho</th>
                    <th class="px-4 py-3 text-left font-semibold">Trạng thái</th>
                    <th class="px-4 py-3 text-right font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sanPhams as $sp)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-mono font-bold text-gray-800">{{ $sp->sku ?: '—' }}</span>
                            <span class="block text-xs text-gray-400">#{{ $sp->maSP }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($sp->hinhAnh)
                                    <img src="{{ asset('storage/'.$sp->hinhAnh) }}" alt="{{ $sp->tenSP }}"
                                         class="w-12 h-12 rounded-lg object-cover bg-gray-100 shrink-0" loading="lazy">
                                @else
                                    <span class="w-12 h-12 rounded-lg bg-bhx-50 text-bhx-300 flex items-center justify-center text-xl shrink-0">
                                        <i class="bi bi-basket"></i>
                                    </span>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800 truncate max-w-[220px]" title="{{ $sp->tenSP }}">{{ $sp->tenSP }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="bhx-tag bg-gray-100 text-gray-600">{{ $sp->danhMuc->tenDM ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $sp->nhaCungCap->tenNCC ?? '—' }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <span class="bhx-price">{{ number_format($sp->giaBan, 0, ',', '.') }}đ</span>
                            <span class="block text-[11px] text-gray-400">/{{ $sp->donVi }}</span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <span class="font-semibold {{ $sp->soLuong <= 0 ? 'text-red-600' : ($sp->soLuong <= 5 ? 'text-amber-600' : 'text-gray-800') }}">
                                {{ $sp->soLuong }} {{ $sp->donVi }}
                            </span>
                            @if($sp->soLuong <= 0)
                                <span class="block text-[11px] font-medium text-red-500">Hết hàng</span>
                            @elseif($sp->soLuong <= 5)
                                <span class="block text-[11px] font-medium text-amber-600">Sắp hết</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($sp->trangThai == \App\Models\SanPham::DANG_BAN)
                                <span class="bhx-tag bg-green-50 text-green-700"><i class="bi bi-dot"></i>Đang bán</span>
                            @else
                                <span class="bhx-tag bg-gray-100 text-gray-500"><i class="bi bi-dot"></i>Ngừng bán</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('staff.sanpham.edit', $sp) }}" title="Sửa sản phẩm"
                                   class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('staff.sanpham.destroy', $sp) }}" class="inline" data-confirm="Xóa sản phẩm {{ $sp->tenSP }}?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Xóa sản phẩm"
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <div class="text-bhx-200 text-5xl mb-3"><i class="bi bi-basket"></i></div>
                            <p class="text-gray-500 font-medium">Không tìm thấy sản phẩm nào</p>
                            <p class="text-sm text-gray-400 mt-1 mb-4">Thử thay đổi từ khóa hoặc bộ lọc</p>
                            @if($hasFilter)
                                <a href="{{ route('staff.sanpham.index') }}" class="text-bhx-600 hover:underline text-sm font-medium">Xóa bộ lọc</a>
                            @else
                                <a href="{{ route('staff.sanpham.create') }}" class="bhx-btn-primary text-sm">Thêm sản phẩm đầu tiên</a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $sanPhams->withQueryString()->links() }}</div>
@endsection
