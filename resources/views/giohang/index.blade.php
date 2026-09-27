@extends('layouts.app')
@section('title', 'Giỏ hàng')
@section('content')
<h1 class="text-2xl font-bold mb-4 flex items-center gap-2">
    <i class="bi bi-cart3 text-bhx-500"></i> Giỏ hàng
</h1>

@if($gioHang && $gioHang->chiTietGioHangs->count() > 0)
@if($pricesChanged)
<p class="mb-4 rounded-lg bg-yellow-50 p-3 text-sm text-yellow-800">Giá sản phẩm đã thay đổi. Giỏ hàng đang hiển thị giá bán hiện tại.</p>
@endif
<div class="bhx-card overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead class="bg-gray-50">
            <tr class="text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3 text-left font-semibold">Sản phẩm</th>
                <th class="px-4 py-3 text-right font-semibold">Đơn giá</th>
                <th class="px-4 py-3 text-center font-semibold">Số lượng</th>
                <th class="px-4 py-3 text-right font-semibold">Thành tiền</th>
                <th class="px-4 py-3 text-center font-semibold">Xóa</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @foreach($gioHang->chiTietGioHangs as $ct)
            <tr class="hover:bg-bhx-50/50 transition">
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if($ct->sanPham?->hinhAnh)
                            <img src="{{ asset('storage/'.$ct->sanPham->hinhAnh) }}" alt="{{ $ct->sanPham->tenSP }}" class="w-14 h-14 object-cover rounded-lg">
                        @else
                            <div class="w-14 h-14 bg-bhx-50 flex items-center justify-center rounded-lg text-bhx-300 text-xl"><i class="bi bi-basket"></i></div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-800">{{ $ct->sanPham->tenSP ?? 'SP đã xóa' }}</p>
                            @if($ct->sanPham)
                                <span class="text-xs text-gray-400">{{ $ct->sanPham->danhMuc->tenDM ?? '' }}</span>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-right">{{ number_format($ct->donGia, 0, ',', '.') }}đ</td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('giohang.update', $ct) }}" class="flex items-center justify-center gap-1">
                        @csrf @method('PUT')
                        <button type="button" onclick="this.nextElementSibling.stepDown(); this.closest('form').submit()" class="w-7 h-7 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition" title="Giảm">−</button>
                        <input type="number" name="soLuong" value="{{ $ct->soLuong }}" min="1" class="border border-gray-200 rounded-lg w-14 text-center px-1 py-1.5 text-sm focus:ring-2 focus:ring-bhx-500 focus:outline-none">
                        <button type="button" onclick="this.previousElementSibling.stepUp(); this.closest('form').submit()" class="w-7 h-7 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition" title="Tăng">+</button>
                    </form>
                </td>
                <td class="px-4 py-3 text-right bhx-price">{{ number_format($ct->thanhTien, 0, ',', '.') }}đ</td>
                <td class="px-4 py-3 text-center">
                    <form method="POST" action="{{ route('giohang.remove', $ct) }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-bhx-red hover:underline text-sm" onclick="return confirm('Xóa sản phẩm này?')">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-bhx-50/60">
            <tr>
                <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-700">Tổng tiền:</td>
                <td class="px-4 py-3 text-right font-extrabold text-bhx-orange text-lg">{{ number_format($gioHang->tongTien, 0, ',', '.') }}đ</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>

<div class="mt-4 flex justify-between items-center">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 bg-gray-200 text-gray-700 px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
        <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
    </a>
    <a href="{{ route('checkout') }}" class="bhx-btn-orange !px-8">
        Đặt hàng <i class="bi bi-arrow-right"></i>
    </a>
</div>
@else
<div class="bhx-card text-center py-16">
    <div class="text-6xl mb-4 text-bhx-300"><i class="bi bi-cart-x"></i></div>
    <p class="text-gray-500 text-lg mb-4">Giỏ hàng trống</p>
    <a href="{{ route('home') }}" class="bhx-btn-primary">Mua sắm ngay</a>
</div>
@endif
@endsection
