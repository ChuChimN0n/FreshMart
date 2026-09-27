@extends('layouts.app')
@section('title', 'Chi tiết đơn ' . ($donHang->maDon ?: '#'.$donHang->maDH))
@section('content')
<a href="{{ route('donhang.index') }}" class="inline-flex items-center gap-1.5 text-bhx-600 hover:underline mb-4 font-medium">
    <i class="bi bi-arrow-left"></i> Quay lại danh sách
</a>

@php
    $steps = [
        \App\Models\DonHang::CHO_XAC_NHAN => ['label' => 'Chờ xác nhận', 'icon' => 'bi-hourglass-split'],
        \App\Models\DonHang::DA_XAC_NHAN => ['label' => 'Đã xác nhận', 'icon' => 'bi-check-circle'],
        \App\Models\DonHang::DANG_GIAO => ['label' => 'Đang giao', 'icon' => 'bi-truck'],
        \App\Models\DonHang::HOAN_THANH => ['label' => 'Hoàn thành', 'icon' => 'bi-flag'],
    ];
    $order = array_keys($steps);
    $cancelled = $donHang->trangThai === \App\Models\DonHang::DA_HUY;
    $currentIdx = array_search($donHang->trangThai, $order);
@endphp
@if($cancelled)
    <div class="bhx-card p-4 mb-6 flex items-center gap-3 text-sm">
        <span class="w-9 h-9 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-base shrink-0"><i class="bi bi-x-circle"></i></span>
        <p class="text-gray-600">Đơn hàng đã được hủy. Bạn có thể đặt lại các sản phẩm này từ <a href="{{ route('home') }}" class="text-bhx-600 hover:underline font-medium">cửa hàng</a>.</p>
    </div>
@else
    <div class="bhx-card p-5 mb-6 overflow-x-auto">
        <div class="flex items-center min-w-[480px]">
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

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bhx-card p-5">
        <h2 class="font-bold mb-3 text-gray-800"><i class="bi bi-info-circle text-bhx-500"></i> Thông tin đơn hàng</h2>
        <div class="space-y-2 text-sm">
            <p><strong>Mã đơn:</strong> <span class="font-semibold text-bhx-700">{{ $donHang->maDon ?: '#'.$donHang->maDH }}</span></p>
            <p><strong>Ngày đặt:</strong> {{ $donHang->ngayDat->format('d/m/Y H:i') }}</p>
            <p class="flex items-center gap-2"><strong>Trạng thái:</strong>
                <span class="px-2 py-1 rounded text-xs font-medium {{ $donHang->trangThaiBadge }}">
                    {{ $donHang->trangThaiLabel }}
                </span>
            </p>
        </div>
        <hr class="my-3">
        <div class="space-y-2 text-sm">
            <p><strong>Người nhận:</strong> {{ $donHang->tenNguoiNhan }}</p>
            <p><strong>Số ĐT:</strong> {{ $donHang->soDienThoai }}</p>
            <p><strong>Địa chỉ:</strong> {{ $donHang->diaChi }}</p>
        </div>
        <p class="mt-4 text-lg font-extrabold text-bhx-orange">Tổng: {{ number_format($donHang->tongTien, 0, ',', '.') }}đ</p>
    </div>

    <div class="bhx-card p-5">
        <h2 class="font-bold mb-3 text-gray-800"><i class="bi bi-bag-check text-bhx-500"></i> Chi tiết sản phẩm</h2>
        <table class="w-full text-sm">
            <thead class="bg-bhx-50 rounded-lg">
                <tr>
                    <th class="px-3 py-2 text-left">Sản phẩm</th>
                    <th class="px-3 py-2 text-right">Đơn giá</th>
                    <th class="px-3 py-2 text-center">SL</th>
                    <th class="px-3 py-2 text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donHang->chiTietDonHangs as $ct)
                <tr class="border-t hover:bg-bhx-50/50">
                    <td class="px-3 py-2">{{ $ct->sanPham->tenSP ?? 'SP đã xóa' }}</td>
                    <td class="px-3 py-2 text-right">{{ number_format($ct->donGia, 0, ',', '.') }}đ</td>
                    <td class="px-3 py-2 text-center">{{ $ct->soLuong }} {{ $ct->sanPham->donVi ?? 'kg' }}</td>
                    <td class="px-3 py-2 text-right bhx-price">{{ number_format($ct->thanhTien, 0, ',', '.') }}đ</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@if($donHang->trangThai == \App\Models\DonHang::HOAN_THANH && $donHang->chiTietDonHangs->count())
<div class="mt-6 bhx-card p-5">
    <h2 class="font-bold mb-4 text-gray-800"><i class="bi bi-star text-bhx-yellow"></i> Đánh giá sản phẩm</h2>
    <p class="text-sm text-gray-500 mb-4">Bạn có thể đánh giá từng sản phẩm trong đơn hàng này.</p>

    @foreach($donHang->chiTietDonHangs as $ct)
        @php
            $spId = $ct->sanPham->maSP ?? null;
            $spTen = $ct->sanPham->tenSP ?? 'SP đã xóa';
            $danhGia = $daDanhGia->firstWhere('maSP', $spId);
        @endphp
        <div class="border rounded-xl p-4 mb-3 {{ $danhGia ? 'bg-bhx-50' : '' }}">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <span class="font-medium">{{ $spTen }}</span>
                    <span class="text-gray-500 text-sm ml-2">({{ $ct->soLuong }} {{ $ct->sanPham->donVi ?? 'kg' }} × {{ number_format($ct->donGia, 0, ',', '.') }}đ)</span>
                </div>

                @if($danhGia)
                    <span class="text-bhx-600 text-sm font-medium"><i class="bi bi-check-circle-fill"></i> Đã đánh giá {{ str_repeat('⭐', $danhGia->soSao) }}</span>
                @else
                    <button type="button" onclick="toggleReviewForm({{ $spId }})" class="bg-bhx-500 hover:bg-bhx-600 text-white px-3 py-1.5 rounded-lg text-sm transition" id="btn-review-{{ $spId }}">Đánh giá</button>
                @endif
            </div>

            @if(!$danhGia)
            <form method="POST" action="{{ route('danhgia.store') }}" class="mt-3 hidden" id="form-review-{{ $spId }}">
                @csrf
                <input type="hidden" name="maSP" value="{{ $spId }}">
                <div class="flex items-center gap-1 mb-2 star-input">
                    @for($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer leading-none">
                        <input type="radio" name="soSao" value="{{ $i }}" {{ $i == 5 ? 'checked' : '' }} class="hidden">
                        <i class="bi bi-star-fill text-3xl"></i>
                    </label>
                    @endfor
                    <span class="star-text ml-2 text-sm font-bold text-bhx-orange"></span>
                </div>
                <textarea name="noiDung" rows="2" class="w-full border rounded-lg px-3 py-2 mb-2 text-sm focus:ring-2 focus:ring-bhx-500 focus:outline-none" placeholder="Nhận xét về {{ $spTen }}..." required></textarea>
                <div class="flex gap-2">
                    <button type="submit" class="bhx-btn-primary !py-2 !px-4 text-sm">Gửi đánh giá</button>
                    <button type="button" onclick="toggleReviewForm({{ $spId }})" class="bg-gray-300 text-gray-700 px-4 py-1.5 rounded-lg text-sm hover:bg-gray-400">Hủy</button>
                </div>
            </form>
            @endif
        </div>
    @endforeach
</div>
@endif

<script>
function toggleReviewForm(spId) {
    const form = document.getElementById('form-review-' + spId);
    form.classList.toggle('hidden');
}

document.querySelectorAll('.star-input').forEach(function (wrap) {
    function paint(n) {
        wrap.querySelectorAll('label').forEach(function (lb, idx) {
            lb.querySelector('i').className = 'bi text-3xl ' + (idx < n ? 'bi-star-fill text-bhx-orange' : 'bi-star-fill text-gray-300');
        });
        const t = wrap.querySelector('.star-text');
        if (t) t.textContent = n + '/5';
    }
    wrap.querySelectorAll('input[name="soSao"]').forEach(function (radio) {
        radio.addEventListener('change', function () { paint(parseInt(radio.value, 10)); });
        radio.closest('label').addEventListener('mouseenter', function () { paint(parseInt(radio.value, 10)); });
    });
    wrap.addEventListener('mouseleave', function () {
        const cur = wrap.querySelector('input[name="soSao"]:checked');
        paint(cur ? parseInt(cur.value, 10) : 5);
    });
    const init = wrap.querySelector('input[name="soSao"]:checked');
    paint(init ? parseInt(init.value, 10) : 5);
});
</script>
@endsection