@extends('layouts.management')
@section('title', 'Admin Dashboard')
@section('content')
<x-page-header title="Dashboard Quản trị" :subtitle="'Xin chào, '.Auth::user()->hoTen" />

{{-- ===== KPI ===== --}}
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-5">
    <x-stat-card :url="route('staff.sanpham.index')" :number="$tongSanPham" :label="'Còn hàng: '.$sanPhamConHang" icon="bi-box-seam" color="bhx" />
    <x-stat-card :url="route('staff.donhang.index')" :number="$tongDonHang" :label="'Chờ xác nhận: '.$donChoXacNhan" icon="bi-receipt" color="blue" />
    <x-stat-card :url="route('admin.taikhoan.index')" :number="$tongKhachHang" label="Tổng khách hàng" icon="bi-people" color="purple" />
    <a href="{{ route('admin.baocao.doanhthu', ['tuNgay' => now()->subDays(30)->format('Y-m-d'), 'denNgay' => now()->format('Y-m-d')]) }}"
       class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 hover:shadow-md transition">
        <span class="w-11 h-11 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-xl shrink-0">
            <i class="bi bi-cash-stack"></i>
        </span>
        <span class="min-w-0">
            <span class="block text-2xl font-bold text-gray-800 leading-none truncate">{{ number_format($doanhThu, 0, ',', '.') }}đ</span>
            <span class="block text-xs text-gray-500 mt-1">Doanh thu</span>
            @if($tangTruong !== null)
                <span class="inline-flex items-center gap-1 text-xs font-semibold mt-0.5 {{ $tangTruong >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    <i class="bi {{ $tangTruong >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i>
                    {{ number_format(abs($tangTruong), 1) }}% so với tháng trước
                </span>
            @endif
        </span>
    </a>
</div>

{{-- ===== Biểu đồ doanh thu 14 ngày ===== --}}
@php $maxTien = max(array_merge([1], array_column($doanhThu14Ngay, 'tien'))); @endphp
<div class="bg-white rounded-xl shadow-sm p-5 mb-5">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-bold text-gray-800">Doanh thu 14 ngày gần nhất</h3>
            <p class="text-xs text-gray-400 mt-0.5">Đơn đã xác nhận, đang giao và hoàn thành</p>
        </div>
        <a href="{{ route('admin.baocao.doanhthu', ['tuNgay' => now()->subDays(30)->format('Y-m-d'), 'denNgay' => now()->format('Y-m-d')]) }}" class="text-sm text-bhx-600 hover:underline font-medium shrink-0">Chi tiết</a>
    </div>
    <div class="flex items-end gap-1.5 sm:gap-2.5 h-48">
        @foreach($doanhThu14Ngay as $i => $d)
            @php $h = max(round($d['tien'] / $maxTien * 100), $d['tien'] > 0 ? 4 : 1); @endphp
            <div class="flex-1 flex flex-col items-center justify-end h-full min-w-0" title="{{ $d['ngay'] }}: {{ number_format($d['tien'], 0, ',', '.') }}đ">
                <div class="w-full max-w-10 rounded-t-md {{ $d['tien'] > 0 ? 'bg-bhx-500 hover:bg-bhx-600' : 'bg-gray-100' }} transition" style="height: {{ $h }}%"></div>
                <span class="text-[10px] mt-1.5 shrink-0 {{ $i % 2 === 0 ? 'text-gray-400' : 'text-transparent' }}">{{ $d['ngay'] }}</span>
            </div>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-5 gap-5 mb-5">
    {{-- ===== Việc cần xử lý ===== --}}
    <div class="xl:col-span-3 bg-white rounded-xl shadow-sm p-5">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="bi bi-lightning-charge text-amber-500"></i> Việc cần xử lý
        </h3>

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
    </div>

    {{-- ===== Cơ cấu đơn + đơn mới ===== --}}
    <div class="xl:col-span-2 space-y-5">
        <div class="bg-white rounded-xl shadow-sm p-5">
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

        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-800">Đơn hàng mới nhất</h3>
                <a href="{{ route('staff.donhang.index') }}" class="text-sm text-bhx-600 hover:underline font-medium">Xem tất cả</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($donHangMoiNhat as $dh)
                    <div class="flex justify-between items-center gap-3 py-2.5 text-sm">
                        <div class="min-w-0">
                            <a href="{{ route('staff.donhang.detail', $dh) }}" class="font-semibold text-bhx-700 hover:underline">{{ $dh->maDon ?: '#'.$dh->maDH }}</a>
                            <span class="text-gray-500 truncate"> · {{ $dh->taiKhoan->hoTen ?? '' }}</span>
                        </div>
                        <span class="bhx-tag {{ $dh->trangThaiBadge }} shrink-0">{{ $dh->trangThaiLabel }}</span>
                    </div>
                @empty
                    <x-empty-state icon="bi-receipt" title="Chưa có đơn hàng" />
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ===== Quản lý nhanh ===== --}}
<div class="bg-white rounded-xl shadow-sm p-5">
    <h3 class="font-bold text-gray-800 mb-4">Quản lý nhanh</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5">
        @foreach([
            [route('staff.sanpham.index'), 'bi-box-seam', 'Sản phẩm', 'bg-bhx-50 text-bhx-700 hover:bg-bhx-100'],
            [route('staff.danhmuc.index'), 'bi-tags', 'Danh mục', 'bg-blue-50 text-blue-700 hover:bg-blue-100'],
            [route('staff.donhang.index'), 'bi-receipt', 'Đơn hàng', 'bg-purple-50 text-purple-700 hover:bg-purple-100'],
            [route('staff.danhgia.index'), 'bi-star', 'Đánh giá', 'bg-amber-50 text-amber-700 hover:bg-amber-100'],
            [route('admin.baocao.index'), 'bi-bar-chart', 'Báo cáo', 'bg-orange-50 text-bhx-orange hover:bg-orange-100'],
            [route('admin.nhacungcap.index'), 'bi-truck', 'Nhà cung cấp', 'bg-teal-50 text-teal-700 hover:bg-teal-100'],
            [route('admin.taikhoan.index'), 'bi-people', 'Tài khoản', 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100'],
            [route('admin.vaitro.index'), 'bi-shield-lock', 'Vai trò', 'bg-pink-50 text-pink-700 hover:bg-pink-100'],
        ] as [$url, $icon, $label, $cls])
            <a href="{{ $url }}" class="flex items-center gap-2.5 p-3 rounded-xl text-sm font-medium transition {{ $cls }}">
                <i class="bi {{ $icon }} text-lg"></i> {{ $label }}
            </a>
        @endforeach
    </div>
</div>
@endsection
