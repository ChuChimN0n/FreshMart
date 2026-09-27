@extends('layouts.management')
@section('title', 'Quản lý tài khoản')
@section('content')
@php $hasFilter = request()->filled('search') || request()->filled('maVT') || request()->filled('trangThai'); @endphp
<x-page-header title="Quản lý tài khoản"
    :subtitle="'Tổng '.$taiKhoans->total().' tài khoản'"
    :actionUrl="route('admin.taikhoan.create')" actionLabel="Thêm tài khoản" />

<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4 mb-5">
    <x-stat-card :url="route('admin.taikhoan.index')" :number="$thongKe['tong']" label="Tổng tài khoản" icon="bi-people" color="bhx" :active="!$hasFilter" />
    <x-stat-card :url="route('admin.taikhoan.index', ['maVT' => \App\Models\VaiTro::ADMIN_ID])" :number="$thongKe['admin']" label="Quản trị viên" icon="bi-shield-check" color="red" :active="request('maVT') == \App\Models\VaiTro::ADMIN_ID" />
    <x-stat-card :url="route('admin.taikhoan.index', ['maVT' => \App\Models\VaiTro::STAFF_ID])" :number="$thongKe['staff']" label="Nhân viên" icon="bi-person-badge" color="blue" :active="request('maVT') == \App\Models\VaiTro::STAFF_ID" />
    <x-stat-card :url="route('admin.taikhoan.index', ['maVT' => \App\Models\VaiTro::KHACH_HANG_ID])" :number="$thongKe['khach']" label="Khách hàng" icon="bi-person" color="green" :active="request('maVT') == \App\Models\VaiTro::KHACH_HANG_ID" />
    <x-stat-card :url="route('admin.taikhoan.index', ['trangThai' => 'KHOA'])" :number="$thongKe['khoa']" label="Đang bị khóa" icon="bi-lock" color="amber" :active="request('trangThai') === 'KHOA'" />
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-col lg:flex-row gap-3">
        <x-toolbar-search name="search" placeholder="Tìm theo tên, tên đăng nhập, email..." />
        <div class="grid grid-cols-2 gap-3 lg:w-auto">
            <select name="maVT" onchange="this.form.submit()" class="bhx-input lg:w-44">
                <option value="">Mọi vai trò</option>
                @foreach($vaiTros as $vt)
                    <option value="{{ $vt->maVT }}" {{ request('maVT') == $vt->maVT ? 'selected' : '' }}>{{ $vt->tenVT }}</option>
                @endforeach
            </select>
            <select name="trangThai" onchange="this.form.submit()" class="bhx-input lg:w-40">
                <option value="">Mọi trạng thái</option>
                <option value="HOAT_DONG" {{ request('trangThai') === 'HOAT_DONG' ? 'selected' : '' }}>Hoạt động</option>
                <option value="KHOA" {{ request('trangThai') === 'KHOA' ? 'selected' : '' }}>Bị khóa</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bhx-btn-primary flex-1 lg:flex-none">Tìm</button>
            @if($hasFilter)
                <a href="{{ route('admin.taikhoan.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition" title="Xóa tất cả lọc">
                    <i class="bi bi-arrow-counterclockwise"></i> Đặt lại
                </a>
            @endif
        </div>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm min-w-[760px]">
            <thead class="bg-gray-50">
                <tr class="text-xs uppercase tracking-wide text-gray-500">
                    <th class="px-4 py-3 text-left font-semibold">Tài khoản</th>
                    <th class="px-4 py-3 text-left font-semibold">Vai trò</th>
                    <th class="px-4 py-3 text-left font-semibold">Trạng thái</th>
                    <th class="px-4 py-3 text-right font-semibold">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($taiKhoans as $tk)
                    <tr class="hover:bg-bhx-50/50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="w-10 h-10 rounded-full bg-bhx-100 text-bhx-700 flex items-center justify-center font-bold shrink-0">
                                    {{ mb_strtoupper(mb_substr($tk->hoTen, 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800">{{ $tk->hoTen }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $tk->tenDangNhap }} · {{ $tk->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($tk->isAdmin())
                                <span class="bhx-tag bg-red-50 text-red-700">{{ $tk->vaiTro->tenVT ?? '' }}</span>
                            @elseif($tk->isStaff())
                                <span class="bhx-tag bg-blue-50 text-blue-700">{{ $tk->vaiTro->tenVT ?? '' }}</span>
                            @else
                                <span class="bhx-tag bg-gray-100 text-gray-600">{{ $tk->vaiTro->tenVT ?? '' }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($tk->trangThai == 'HOAT_DONG')
                                <span class="bhx-tag bg-green-50 text-green-700"><i class="bi bi-dot"></i>Hoạt động</span>
                            @else
                                <span class="bhx-tag bg-red-50 text-red-700"><i class="bi bi-dot"></i>Bị khóa</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.taikhoan.edit', $tk) }}" title="{{ $tk->isCustomer() ? 'Xem tài khoản' : 'Sửa tài khoản' }}"
                                   class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                                    <i class="bi {{ $tk->isCustomer() ? 'bi-eye' : 'bi-pencil' }}"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.taikhoan.toggle', $tk) }}" class="inline">
                                    @csrf @method('PATCH')
                                    @if($tk->trangThai == 'HOAT_DONG')
                                        <button type="submit" title="Khóa tài khoản"
                                                class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">
                                            <i class="bi bi-lock"></i>
                                        </button>
                                    @else
                                        <button type="submit" title="Mở khóa tài khoản"
                                                class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 flex items-center justify-center transition">
                                            <i class="bi bi-unlock"></i>
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <x-empty-state icon="bi-people" title="Không tìm thấy tài khoản nào"
                                desc="Thử thay đổi từ khóa hoặc bộ lọc" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $taiKhoans->withQueryString()->links() }}</div>
@endsection
