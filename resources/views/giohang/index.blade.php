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
                <td class="px-4 py-3 align-middle">
                    <div class="flex items-center gap-3">
                        @if($ct->sanPham?->hinhAnh)
                            <img src="{{ asset('storage/'.$ct->sanPham->hinhAnh) }}" alt="{{ $ct->sanPham->tenSP }}" class="w-14 h-14 object-cover rounded-lg">
                        @else
                            <div class="w-14 h-14 bg-bhx-50 flex items-center justify-center rounded-lg text-bhx-300 text-xl"><i class="bi bi-basket"></i></div>
                        @endif
                        <div>
                            @if($ct->sanPham)
                                <a href="{{ route('home.show', $ct->sanPham) }}" class="font-medium text-gray-800 hover:text-bhx-600 transition">{{ $ct->sanPham->tenSP }}</a>
                                <span class="block text-xs text-gray-400">{{ $ct->sanPham->danhMuc->tenDM ?? '' }}</span>
                            @else
                                <p class="font-medium text-gray-800">SP đã xóa</p>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3 text-right align-middle">{{ number_format($ct->donGia, 0, ',', '.') }}đ</td>
                <td class="px-4 py-4 align-middle">
                    <div class="relative flex justify-center">
                        @php $tonKho = $ct->sanPham?->soLuong ?? $ct->soLuong; @endphp
                        <form method="POST" action="{{ route('giohang.update', $ct) }}" data-stepper>
                            @csrf @method('PUT')
                            <div class="inline-flex items-stretch rounded-md border border-gray-200 bg-white shadow-sm overflow-hidden">
                                <button type="button" data-step="-1" title="Giảm"
                                    class="w-9 h-9 flex items-center justify-center leading-none text-gray-600 transition hover:bg-gray-50 active:bg-gray-100 disabled:opacity-30 disabled:pointer-events-none"
                                    {{ $ct->soLuong <= 1 ? 'disabled' : '' }}>
                                    <i class="bi bi-dash leading-none"></i>
                                </button>
                                <input type="number" name="soLuong" value="{{ $ct->soLuong }}" min="1" max="{{ $tonKho }}" data-max="{{ $tonKho }}"
                                    class="w-12 h-9 p-0 text-center font-medium text-gray-800 text-sm border-x border-gray-200 focus:ring-0 focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <button type="button" data-step="1" title="{{ $ct->soLuong >= $tonKho ? 'Chỉ còn '.$tonKho.' sản phẩm' : 'Tăng' }}"
                                    class="w-9 h-9 flex items-center justify-center leading-none text-gray-600 transition hover:bg-gray-50 active:bg-gray-100 disabled:opacity-30 disabled:pointer-events-none"
                                    {{ $ct->soLuong >= $tonKho ? 'disabled' : '' }}>
                                    <i class="bi bi-plus leading-none"></i>
                                </button>
                            </div>
                        </form>
                        <span class="absolute top-full mt-1 left-1/2 -translate-x-1/2 whitespace-nowrap text-[11px] leading-none text-gray-400">Còn {{ $tonKho }}</span>
                    </div>
                    <script>
                        (function () {
                            var wrap = document.currentScript.previousElementSibling;
                            var form = wrap && wrap.tagName === 'FORM'
                                ? wrap
                                : (wrap ? wrap.querySelector('form[data-stepper]') : null);
                            if (!form) return;
                            var input = form.querySelector('input[name=soLuong]');
                            var max = parseInt(input.dataset.max) || 1;
                            form.querySelectorAll('[data-step]').forEach(function (btn) {
                                btn.addEventListener('click', function () {
                                    var next = (parseInt(input.value) || 1) + parseInt(btn.dataset.step);
                                    next = Math.min(Math.max(next, 1), max);
                                    if (next === parseInt(input.value)) return;
                                    input.value = next;
                                    btn.disabled = true;
                                    form.submit();
                                });
                            });
                            input.addEventListener('change', function () {
                                var next = parseInt(input.value) || 1;
                                input.value = Math.min(Math.max(next, 1), max);
                                form.submit();
                            });
                        })();
                    </script>
                </td>
                <td class="px-4 py-3 text-right bhx-price align-middle">{{ number_format($ct->thanhTien, 0, ',', '.') }}đ</td>
                <td class="px-4 py-3 text-center align-middle">
                    <form method="POST" action="{{ route('giohang.remove', $ct) }}" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" title="Xóa sản phẩm"
                            class="w-8 h-8 rounded-lg bg-red-50 text-bhx-red hover:bg-red-100 inline-flex items-center justify-center transition"
                            onclick="return confirm('Xóa sản phẩm này?')">
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
