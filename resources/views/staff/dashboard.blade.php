@extends('layouts.management')
@section('title', 'Staff Dashboard')
@section('content')
@php
    $canOrders = Auth::user()->hasPermission(\App\Models\Quyen::ORDERS);
    $canProducts = Auth::user()->hasPermission(\App\Models\Quyen::PRODUCTS);
@endphp
<x-page-header title="Dashboard Nhân viên" :subtitle="'Xin chào, '.Auth::user()->hoTen" />

{{-- ===== KPI ===== --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
    <x-stat-card :url="route('staff.sanpham.index')" :number="$tongSanPham" :label="'Còn hàng: '.$sanPhamConHang" icon="bi-box-seam" color="bhx" />
    <x-stat-card :url="route('staff.donhang.index')" :number="$tongDonHang" :label="'Chờ xác nhận: '.$donChoXacNhan" icon="bi-receipt" color="blue" />
</div>

{{-- ===== Biểu đồ doanh thu 14 ngày (quyền đơn hàng) ===== --}}
@if($canOrders)
    @php $maxTien = max(array_merge([1], array_column($doanhThu14Ngay, 'tien'))); @endphp
    <div class="bg-white rounded-xl shadow-sm p-5 mb-5">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-gray-800">Doanh thu 14 ngày gần nhất</h3>
                <p class="text-xs text-gray-400 mt-0.5">Đơn đã xác nhận, đang giao và hoàn thành</p>
            </div>
        </div>
        <div class="flex items-end gap-1.5 sm:gap-2.5 h-44">
            @foreach($doanhThu14Ngay as $i => $d)
                @php $h = max(round($d['tien'] / $maxTien * 100), $d['tien'] > 0 ? 4 : 1); @endphp
                <div class="flex-1 flex flex-col items-center justify-end h-full min-w-0" title="{{ $d['ngay'] }}: {{ number_format($d['tien'], 0, ',', '.') }}đ">
                    <div class="w-full max-w-10 rounded-t-md {{ $d['tien'] > 0 ? 'bg-bhx-500 hover:bg-bhx-600' : 'bg-gray-100' }} transition" style="height: {{ $h }}%"></div>
                    <span class="text-[10px] mt-1.5 shrink-0 {{ $i % 2 === 0 ? 'text-gray-400' : 'text-transparent' }}">{{ $d['ngay'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="grid grid-cols-1 xl:grid-cols-5 gap-5 mb-5">
    {{-- ===== Việc cần xử lý ===== --}}
    @if($canOrders || $canProducts)
        <div class="xl:col-span-3 bg-white rounded-xl shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="bi bi-lightning-charge text-amber-500"></i> Việc cần xử lý
            </h3>

            @if($canOrders)
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Đơn chờ xác nhận ({{ $donCanXuLy->count() }})</p>
                <div class="divide-y divide-gray-100 mb-4">
                    @forelse($donCanXuLy as $dh)
                        <div class="flex items-center gap-3 py-2.5 text-sm">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('staff.donhang.detail', $dh) }}" class="font-semibold text-bhx-700 hover:underline">{{ $dh->maDon ?: '#'.$dh->maDH }}</a>
                                <span class="text-gray-500 truncate"> · {{ $dh->taiKhoan->hoTen ?? '' }}</span>
                                <span class="block text-xs text-gray-400">{{ $dh->ngayDat->format('H:i d/m/Y') }} · <span class="font-semibold text-gray-600">{{ number_format($dh->tongTien, 0, ',', '.') }}đ</span></span>
                            </div>
                            <a href="{{ route('staff.donhang.detail', $dh) }}" class="text-xs font-semibold text-white bg-bhx-500 hover:bg-bhx-600 rounded-lg px-3 py-1.5 transition shrink-0">Duyệt</a>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-2">Không có đơn nào chờ xác nhận. 🎉</p>
                    @endforelse
                </div>
            @endif

            @if($canProducts)
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Sản phẩm sắp hết ({{ $spSapHet->count() }})</p>
                <div class="divide-y divide-gray-100">
                    @forelse($spSapHet as $sp)
                        <div class="flex items-center gap-3 py-2.5 text-sm">
                            @if($sp->hinhAnh)
                                <img src="{{ asset('storage/'.$sp->hinhAnh) }}" alt="" class="w-9 h-9 rounded-lg object-cover bg-gray-100 shrink-0" loading="lazy">
                            @else
                                <span class="w-9 h-9 rounded-lg bg-bhx-50 text-bhx-300 flex items-center justify-center shrink-0"><i class="bi bi-basket"></i></span>
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-800 truncate">{{ $sp->tenSP }}</p>
                                <p class="text-xs text-gray-400">{{ $sp->danhMuc->tenDM ?? '' }}</p>
                            </div>
                            <span class="text-xs font-bold text-amber-600 whitespace-nowrap">Còn {{ $sp->soLuong }} {{ $sp->donVi }}</span>
                            <a href="{{ route('staff.sanpham.edit', $sp) }}" class="text-xs font-semibold text-blue-600 hover:underline shrink-0">Nhập thêm</a>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 py-2">Kho hàng ổn định, không có SP nào sắp hết. 🎉</p>
                    @endforelse
                </div>
            @endif
        </div>
    @endif

    {{-- ===== Cơ cấu đơn (quyền đơn hàng) ===== --}}
    @if($canOrders)
        <div class="{{ ($canOrders || $canProducts) ? 'xl:col-span-2' : 'xl:col-span-5' }} bg-white rounded-xl shadow-sm p-5">
            <h3 class="font-bold text-gray-800 mb-4">Cơ cấu đơn hàng</h3>
            @php
                $tong = max(array_sum($coCauDon), 1);
                $bars = [
                    [\App\Models\DonHang::CHO_XAC_NHAN, 'bg-amber-500'],
                    [\App\Models\DonHang::DA_XAC_NHAN, 'bg-blue-500'],
                    [\App\Models\DonHang::DANG_GIAO, 'bg-purple-500'],
                    [\App\Models\DonHang::HOAN_THANH, 'bg-green-500'],
                    [\App\Models\DonHang::DA_HUY, 'bg-gray-400'],
                ];
            @endphp
            <div class="space-y-3">
                @foreach($bars as [$key, $bar])
                    @php $cnt = $coCauDon[$key] ?? 0; $pct = round($cnt / $tong * 100); @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <a href="{{ route('staff.donhang.index', ['trangThai' => $key]) }}" class="font-medium text-gray-700 hover:text-bhx-600 transition">{{ \App\Models\DonHang::TRANG_THAI[$key] }}</a>
                            <span class="text-gray-400">{{ $cnt }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $bar }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
    <div class="bg-white rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-800">Đơn hàng mới nhất</h3>
            @if($canOrders)
                <a href="{{ route('staff.donhang.index') }}" class="text-sm text-bhx-600 hover:underline font-medium">Xem tất cả</a>
            @endif
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($donHangMoiNhat as $dh)
                <div class="flex justify-between items-center gap-3 py-2.5 text-sm">
                    <div class="min-w-0">
                        <span class="font-semibold text-gray-800">{{ $dh->maDon ?: '#'.$dh->maDH }}</span>
                        <span class="text-gray-500 truncate"> · {{ $dh->taiKhoan->hoTen ?? '' }}</span>
                    </div>
                    <a href="{{ route('staff.donhang.detail', $dh) }}" class="text-bhx-600 hover:underline font-medium shrink-0">Xem</a>
                </div>
            @empty
                <x-empty-state icon="bi-receipt" title="Chưa có đơn hàng" />
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5">
        <h3 class="font-bold text-gray-800 mb-4">Quản lý nhanh</h3>
        <div class="space-y-2.5">
            @if($canProducts)
                <a href="{{ route('staff.sanpham.index') }}" class="flex items-center gap-2.5 bg-bhx-50 p-3 rounded-xl hover:bg-bhx-100 text-sm font-medium text-bhx-700 transition"><i class="bi bi-box-seam text-lg"></i> Quản lý sản phẩm</a>
            @endif
            @if(Auth::user()->hasPermission(\App\Models\Quyen::CATEGORIES))
                <a href="{{ route('staff.danhmuc.index') }}" class="flex items-center gap-2.5 bg-blue-50 p-3 rounded-xl hover:bg-blue-100 text-sm font-medium text-blue-700 transition"><i class="bi bi-tags text-lg"></i> Quản lý danh mục</a>
            @endif
            @if($canOrders)
                <a href="{{ route('staff.donhang.index') }}" class="flex items-center gap-2.5 bg-purple-50 p-3 rounded-xl hover:bg-purple-100 text-sm font-medium text-purple-700 transition"><i class="bi bi-receipt text-lg"></i> Quản lý đơn hàng</a>
            @endif
            @if(Auth::user()->hasPermission(\App\Models\Quyen::REVIEWS))
                <a href="{{ route('staff.danhgia.index') }}" class="flex items-center gap-2.5 bg-amber-50 p-3 rounded-xl hover:bg-amber-100 text-sm font-medium text-amber-700 transition"><i class="bi bi-star text-lg"></i> Quản lý đánh giá</a>
            @endif
        </div>
    </div>
</div>
@endsection
