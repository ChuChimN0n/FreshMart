@extends('layouts.management')
@section('title', 'Chi tiết đơn ' . ($donHang->maDon ?: '#'.$donHang->maDH))
@section('content')
@php
    $steps = [
        \App\Models\DonHang::CHO_XAC_NHAN => ['label' => 'Chờ xác nhận', 'icon' => 'bi-hourglass-split'],
        \App\Models\DonHang::DA_XAC_NHAN => ['label' => 'Đã xác nhận', 'icon' => 'bi-check-circle'],
        \App\Models\DonHang::DANG_GIAO => ['label' => 'Đang giao', 'icon' => 'bi-truck'],
        \App\Models\DonHang::HOAN_THANH => ['label' => 'Hoàn thành', 'icon' => 'bi-flag'],
    ];
    $order = [\App\Models\DonHang::CHO_XAC_NHAN, \App\Models\DonHang::DA_XAC_NHAN, \App\Models\DonHang::DANG_GIAO, \App\Models\DonHang::HOAN_THANH];
    $cancelled = $donHang->trangThai === \App\Models\DonHang::DA_HUY;
    $currentIdx = array_search($donHang->trangThai, $order);
@endphp

<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('staff.donhang.index') }}" class="hover:text-bhx-600 transition">Đơn hàng</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">{{ $donHang->maDon ?: '#'.$donHang->maDH }}</span>
</nav>

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Đơn hàng {{ $donHang->maDon ?: '#'.$donHang->maDH }}</h1>
        <p class="text-sm text-gray-500 mt-1">Đặt lúc {{ $donHang->ngayDat->format('H:i d/m/Y') }} · {{ $donHang->taiKhoan->hoTen ?? 'Khách lẻ' }}</p>
    </div>
    <span class="bhx-tag text-sm px-3 py-1.5 {{ $donHang->trangThaiBadge }}">{{ $donHang->trangThaiLabel }}</span>
</div>

{{-- ===== Timeline trạng thái ===== --}}
@if(! $cancelled)
    <div class="bg-white rounded-xl shadow-sm p-5 mb-5">
        <div class="flex items-center">
            @foreach($order as $i => $key)
                <div class="flex items-center {{ $i < count($order) - 1 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center shrink-0">
                        <span class="w-9 h-9 rounded-full flex items-center justify-center text-base
                            {{ $currentIdx !== false && $i <= $currentIdx ? 'bg-bhx-500 text-white' : 'bg-gray-100 text-gray-400' }}">
                            <i class="bi {{ $steps[$key]['icon'] }}"></i>
                        </span>
                        <span class="text-[11px] mt-1.5 whitespace-nowrap {{ $currentIdx !== false && $i <= $currentIdx ? 'text-bhx-700 font-semibold' : 'text-gray-400' }}">
                            {{ $steps[$key]['label'] }}
                        </span>
                    </div>
                    @if($i < count($order) - 1)
                        <div class="flex-1 h-0.5 mx-2 mb-5 rounded {{ $currentIdx !== false && $i < $currentIdx ? 'bg-bhx-500' : 'bg-gray-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-5 gap-5 items-start">
    {{-- ===== Thông tin đơn ===== --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-7 h-7 rounded-lg bg-bhx-50 text-bhx-600 flex items-center justify-center text-sm"><i class="bi bi-person"></i></span>
            Thông tin nhận hàng
        </h2>
        <dl class="text-sm space-y-2.5">
            <div class="flex justify-between gap-3"><dt class="text-gray-400">Khách hàng</dt><dd class="font-medium text-gray-800 text-right">{{ $donHang->taiKhoan->hoTen ?? '—' }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-gray-400">Người nhận</dt><dd class="font-medium text-gray-800 text-right">{{ $donHang->tenNguoiNhan }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-gray-400">Số điện thoại</dt><dd class="font-medium text-gray-800 text-right">{{ $donHang->soDienThoai }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-gray-400">Địa chỉ</dt><dd class="font-medium text-gray-800 text-right">{{ $donHang->diaChi }}</dd></div>
        </dl>
        <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between items-center">
            <span class="text-sm text-gray-500">Tổng thanh toán</span>
            <span class="text-xl font-bold text-bhx-orange">{{ number_format($donHang->tongTien, 0, ',', '.') }}đ</span>
        </div>
    </div>

    <div class="lg:col-span-3 space-y-5">
        {{-- ===== Sản phẩm ===== --}}
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-bhx-50 text-bhx-600 flex items-center justify-center text-sm"><i class="bi bi-basket"></i></span>
                Sản phẩm ({{ $donHang->chiTietDonHangs->count() }})
            </h2>
            <div class="divide-y divide-gray-100">
                @foreach($donHang->chiTietDonHangs as $ct)
                    <div class="flex items-center gap-3 py-2.5">
                        @if($ct->sanPham?->hinhAnh)
                            <img src="{{ asset('storage/'.$ct->sanPham->hinhAnh) }}" alt="" class="w-11 h-11 rounded-lg object-cover bg-gray-100 shrink-0" loading="lazy">
                        @else
                            <span class="w-11 h-11 rounded-lg bg-bhx-50 text-bhx-300 flex items-center justify-center shrink-0"><i class="bi bi-basket"></i></span>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $ct->sanPham->tenSP ?? 'SP đã xóa' }}</p>
                            <p class="text-xs text-gray-400">{{ number_format($ct->donGia, 0, ',', '.') }}đ × {{ $ct->soLuong }}</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-800 whitespace-nowrap">{{ number_format($ct->thanhTien, 0, ',', '.') }}đ</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ===== Cập nhật trạng thái ===== --}}
        @if($donHang->trangThai != \App\Models\DonHang::HOAN_THANH && $donHang->trangThai != \App\Models\DonHang::DA_HUY)
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm"><i class="bi bi-arrow-repeat"></i></span>
                    Cập nhật trạng thái
                </h2>
                <form method="POST" action="{{ route('staff.donhang.update', $donHang) }}" class="flex flex-col sm:flex-row gap-2">
                    @csrf @method('PATCH')
                    <select name="trangThai" class="bhx-input flex-1">
                        @foreach(\App\Models\DonHang::VALID_TRANSITIONS[$donHang->trangThai] ?? [] as $next)
                            <option value="{{ $next }}">{{ \App\Models\DonHang::TRANG_THAI[$next] }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bhx-btn-primary">Cập nhật</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
