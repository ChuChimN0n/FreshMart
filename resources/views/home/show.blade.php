@extends('layouts.app')
@section('title', $sanpham->tenSP)
@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-1.5 text-sm text-gray-500 mb-4 flex-wrap">
    <a href="{{ route('home') }}" class="hover:text-bhx-600 font-medium">Trang chủ</a>
    <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
    @if($sanpham->danhMuc)
        <a href="{{ route('home', ['maDM' => $sanpham->danhMuc->maDM]) }}" class="hover:text-bhx-600 font-medium">{{ $sanpham->danhMuc->tenDM }}</a>
        <i class="bi bi-chevron-right text-[10px] text-gray-400"></i>
    @endif
    <span class="text-gray-400 truncate max-w-[240px]">{{ $sanpham->tenSP }}</span>
</nav>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

    {{-- Gallery --}}
    <div class="bhx-card p-3 md:p-4">
        <div class="relative aspect-square bg-bhx-50 rounded-lg overflow-hidden group">
            @if($sanpham->hinhAnh)
                <img src="{{ asset('storage/'.$sanpham->hinhAnh) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $sanpham->tenSP }}">
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-bhx-300">
                    <i class="bi bi-basket text-8xl"></i>
                </div>
            @endif
            @if($sanpham->isAvailable())
                <span class="absolute top-3 left-3 bg-bhx-500 text-white text-xs px-3 py-1 rounded-full font-semibold shadow-sm">
                    <i class="bi bi-check-circle-fill"></i> Còn hàng
                </span>
            @else
                <span class="absolute top-3 left-3 bg-gray-700 text-white text-xs px-3 py-1 rounded-full font-semibold shadow-sm">
                    <i class="bi bi-x-circle-fill"></i> Hết hàng
                </span>
            @endif
            @if($soLuongDaBan > 0)
                <span class="absolute bottom-3 right-3 bg-white/90 backdrop-blur text-xs text-gray-700 px-3 py-1 rounded-full font-semibold shadow-sm">
                    Đã bán {{ number_format($soLuongDaBan, 0, ',', '.') }}
                </span>
            @endif
        </div>
    </div>

    {{-- Info --}}
    <div class="bhx-card p-5 md:p-6">
        <div class="flex items-start justify-between gap-3 flex-wrap">
            <div class="min-w-0">
                <h1 class="text-2xl font-extrabold text-gray-900 leading-snug">{{ $sanpham->tenSP }}</h1>
                <div class="mt-2 flex items-center gap-2 flex-wrap">
                    <span class="px-2.5 py-1 bg-bhx-50 text-bhx-700 rounded-full text-xs font-semibold">{{ $sanpham->danhMuc->tenDM ?? 'Thực phẩm' }}</span>
                    <span class="px-2.5 py-1 bg-orange-50 text-bhx-orange rounded-full text-xs font-semibold">Giá tốt mỗi ngày</span>
                </div>
            </div>
            <div class="flex flex-col items-end gap-1">
                <span class="flex items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi {{ $i <= round($trungBinhSao) ? 'bi-star-fill text-bhx-yellow' : 'bi-star text-gray-300' }}"></i>
                    @endfor
                </span>
                @if($tongDanhGia > 0)
                    <a href="#danh-gia" class="text-xs text-bhx-600 hover:underline font-medium">
                        {{ number_format($trungBinhSao, 1, ',', '.') }} ({{ $tongDanhGia }} đánh giá)
                    </a>
                @else
                    <span class="text-xs text-gray-400">Chưa có đánh giá</span>
                @endif
            </div>
        </div>

        {{-- Trust strip --}}
        <div class="mt-4 grid grid-cols-3 gap-2 text-[11px] text-center">
            <div class="bg-bhx-gray rounded-lg px-2 py-2.5 text-gray-600">
                <i class="bi bi-truck text-bhx-600 block text-lg mb-1"></i> Giao nhanh 2-4h
            </div>
            <div class="bg-bhx-gray rounded-lg px-2 py-2.5 text-gray-600">
                <i class="bi bi-arrow-repeat text-bhx-600 block text-lg mb-1"></i> Đổi trả dễ dàng
            </div>
            <div class="bg-bhx-gray rounded-lg px-2 py-2.5 text-gray-600">
                <i class="bi bi-shield-check text-bhx-600 block text-lg mb-1"></i> An toàn vệ sinh
            </div>
        </div>

        {{-- Price box --}}
        <div class="mt-4 rounded-xl border-2 border-bhx-orange/30 bg-orange-50/60 p-4">
            <div class="flex items-end gap-2 flex-wrap">
                <span class="text-3xl md:text-4xl font-extrabold text-bhx-orange leading-none">{{ number_format($sanpham->giaBan, 0, ',', '.') }}đ</span>
                <span class="text-base font-medium text-gray-400 leading-none">/{{ $sanpham->donVi ?? 'kg' }}</span>
            </div>
            @if($sanpham->soLuong > 0)
                <span class="block mt-2 text-xs text-gray-500">Hiện còn <strong class="text-bhx-700">{{ $sanpham->soLuong }} {{ $sanpham->donVi ?? 'kg' }}</strong>. Số lượng có hạn, chỉ áp dụng cho khu vực giao hàng của Bách Hóa Xanh.</span>
            @else
                <span class="block mt-2 text-xs text-bhx-red font-medium">Sản phẩm tạm thời hết hàng.</span>
            @endif
        </div>

        {{-- Quantity + actions --}}
        @if($sanpham->isAvailable())
            <form method="POST" action="{{ route('giohang.add') }}" id="addCartForm" class="mt-5 flex items-center gap-3 flex-wrap">
                @csrf
                <input type="hidden" name="maSP" value="{{ $sanpham->maSP }}">
                <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                    <button type="button" onclick="const q=document.getElementById('qtyInput').querySelector('input'); q.stepDown();" class="px-3 py-2.5 text-gray-500 hover:bg-gray-100">−</button>
                    <span id="qtyInput" class="contents">
                        <input type="number" name="soLuong" value="1" min="1" max="{{ $sanpham->soLuong }}" class="w-16 text-center py-2.5 text-sm focus:outline-none">
                    </span>
                    <button type="button" onclick="const q=document.getElementById('qtyInput').querySelector('input'); q.stepUp();" class="px-3 py-2.5 text-gray-500 hover:bg-gray-100">+</button>
                </div>
                <button type="submit" class="bhx-btn-primary !px-8">
                    <i class="bi bi-cart-plus"></i> Thêm vào giỏ
                </button>
                <button type="button" onclick="buyNow()" class="bhx-btn-orange !px-8">
                    <i class="bi bi-lightning-charge-fill"></i> Mua ngay
                </button>
            </form>
            <div class="mt-3 text-xs text-gray-400 flex items-center gap-1">
                <i class="bi bi-truck"></i> Giao hàng nhanh từ 2 - 4 giờ. Miễn phí giao hàng cho đơn từ 200.000đ.
            </div>
        @else
            <div class="mt-5 px-4 py-3 bg-red-50 text-bhx-red rounded-lg text-sm font-medium">
                <i class="bi bi-x-circle-fill"></i> Sản phẩm hiện đã hết hàng, vui lòng quay lại sau.
            </div>
        @endif
    </div>
</div>

{{-- Tabs: Mô tả / Thông tin chi tiết / Chính sách --}}
<div class="bhx-card mt-6 overflow-hidden">
    <div class="flex border-b border-gray-100 overflow-x-auto" id="detailTabs">
        <button type="button" data-tab="mo-ta" class="tab-btn px-5 py-3.5 text-sm font-semibold whitespace-nowrap border-b-2 border-bhx-500 text-bhx-700">Mô tả sản phẩm</button>
        <button type="button" data-tab="thong-tin" class="tab-btn px-5 py-3.5 text-sm font-semibold whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-gray-700">Thông tin chi tiết</button>
        <button type="button" data-tab="chinh-sach" class="tab-btn px-5 py-3.5 text-sm font-semibold whitespace-nowrap border-b-2 border-transparent text-gray-500 hover:text-gray-700">Chính sách mua hàng</button>
    </div>

    <div id="tab-mo-ta" class="tab-panel p-5">
        <h4 class="font-semibold text-gray-800 mb-2">Giới thiệu {{ $sanpham->tenSP }}</h4>
        @if($sanpham->moTa)
            <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">{{ $sanpham->moTa }}</p>
        @else
            <p class="text-gray-600 text-sm leading-relaxed">
                Sản phẩm {{ $sanpham->tenSP }} thuộc danh mục {{ $sanpham->danhMuc->tenDM ?? 'thực phẩm' }} được Bách Hóa Xanh tuyển chọn kỹ lưỡng từ nhà cung cấp {{ $sanpham->nhaCungCap->tenNCC ?? 'uy tín' }},
                đảm bảo tươi ngon mỗi ngày và an toàn vệ sinh thực phẩm. Sản phẩm được giao nhanh trong ngày và bảo quản đúng quy cách.
            </p>
        @endif
    </div>

    <div id="tab-thong-tin" class="tab-panel p-5 hidden">
        <h4 class="font-semibold text-gray-800 mb-3">Bảng thông tin chi tiết</h4>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-50">
                <tr><td class="py-2.5 text-gray-500 w-40">Tên sản phẩm</td><td class="py-2.5 font-medium text-gray-800">{{ $sanpham->tenSP }}</td></tr>
                <tr><td class="py-2.5 text-gray-500">Mã sản phẩm</td><td class="py-2.5 font-medium text-gray-800">BHX{{ str_pad($sanpham->maSP, 4, '0', STR_PAD_LEFT) }}</td></tr>
                <tr><td class="py-2.5 text-gray-500">Danh mục</td><td class="py-2.5 font-medium text-gray-800">{{ $sanpham->danhMuc->tenDM ?? 'Thực phẩm' }}</td></tr>
                @if($sanpham->nhaCungCap)
                <tr><td class="py-2.5 text-gray-500">Nhà cung cấp</td><td class="py-2.5 font-medium text-gray-800">{{ $sanpham->nhaCungCap->tenNCC }}</td></tr>
                <tr><td class="py-2.5 text-gray-500">Xuất xứ</td><td class="py-2.5 font-medium text-gray-800">{{ $sanpham->nhaCungCap->diaChi ?: '-' }}</td></tr>
                @endif
                <tr><td class="py-2.5 text-gray-500">Đơn vị tính</td><td class="py-2.5 font-medium text-gray-800">{{ $sanpham->donVi ?? 'kg' }}</td></tr>
                <tr><td class="py-2.5 text-gray-500">Đơn giá</td><td class="py-2.5 font-bold text-bhx-orange">{{ number_format($sanpham->giaBan, 0, ',', '.') }}đ/{{ $sanpham->donVi ?? 'kg' }}</td></tr>
                <tr><td class="py-2.5 text-gray-500">Tình trạng</td><td class="py-2.5 font-medium text-gray-800">{{ $sanpham->isAvailable() ? 'Còn hàng' : 'Hết hàng' }}</td></tr>
                <tr><td class="py-2.5 text-gray-500">Số lượng đã bán</td><td class="py-2.5 font-medium text-gray-800">{{ $soLuongDaBan > 0 ? number_format($soLuongDaBan, 0, ',', '.') : 'Chưa bán' }}</td></tr>
            </tbody>
        </table>
    </div>

    <div id="tab-chinh-sach" class="tab-panel p-5 hidden">
        <h4 class="font-semibold text-gray-800 mb-3">Chính sách mua hàng tại Bách Hóa Xanh</h4>
        <ul class="space-y-2.5 text-sm text-gray-600">
            <li class="flex items-start gap-2"><i class="bi bi-check-circle-fill text-bhx-500 mt-0.5"></i> Giao hàng nhanh từ 2 - 4 giờ tại các quận nội thành, miễn phí cho đơn từ 200.000đ.</li>
            <li class="flex items-start gap-2"><i class="bi bi-check-circle-fill text-bhx-500 mt-0.5"></i> Đổi trả hàng trong 7 ngày nếu sản phẩm không đạt chất lượng hoặc không hài lòng.</li>
            <li class="flex items-start gap-2"><i class="bi bi-check-circle-fill text-bhx-500 mt-0.5"></i> Thanh toán linh hoạt: tiền mặt khi nhận hàng (COD) hoặc chuyển khoản.</li>
            <li class="flex items-start gap-2"><i class="bi bi-check-circle-fill text-bhx-500 mt-0.5"></i> Kiểm tra nhanh chất lượng sản phẩm ngay khi giao hàng.</li>
        </ul>
    </div>
</div>

{{-- Đánh giá --}}
<div id="danh-gia" class="mt-10 scroll-mt-32">
    <h2 class="text-xl font-bold flex items-center gap-2">
        <i class="bi bi-chat-left-text text-bhx-500"></i> Đánh giá sản phẩm
        @if($tongDanhGia > 0)
            <span class="text-sm font-normal text-gray-400">({{ $tongDanhGia }} đánh giá)</span>
        @endif
    </h2>

    @if($tongDanhGia > 0)
    <div class="grid grid-cols-1 md:grid-cols-[220px_1fr] gap-4 mt-4">
        <div class="bhx-card p-5 text-center">
            <p class="text-4xl font-extrabold text-gray-900">{{ number_format($trungBinhSao, 1, ',', '.') }}</p>
            <div class="flex justify-center gap-0.5 mt-2 text-lg">
                @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= round($trungBinhSao) ? 'bi-star-fill text-bhx-yellow' : 'bi-star text-gray-300' }}"></i>
                @endfor
            </div>
            <p class="text-xs text-gray-400 mt-2">{{ $tongDanhGia }} lượt đánh giá</p>
        </div>
        <div class="bhx-card p-5 space-y-2">
            @foreach($phanTramSao as $row)
            <div class="flex items-center gap-3 text-sm">
                <span class="w-10 text-gray-600 font-medium shrink-0">{{ $row['soSao'] }} <i class="bi bi-star-fill text-bhx-yellow text-xs"></i></span>
                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-bhx-yellow rounded-full" style="width: {{ $row['phanTram'] }}%"></div>
                </div>
                <span class="w-8 text-right text-gray-400 text-xs shrink-0">{{ $row['soLuong'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Form đánh giá --}}
    <div class="bhx-card p-5 mt-4">
        @auth
            @if($daMua)
                <h3 class="font-semibold text-gray-800 mb-3">Viết đánh giá của bạn</h3>
                @if($errors->any())
                    <div class="mb-3 p-3 bg-red-50 text-bhx-red text-sm rounded-lg">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('danhgia.store') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="maSP" value="{{ $sanpham->maSP }}">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1.5">Số sao</label>
                        <div id="starRating" class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="cursor-pointer">
                                    <input type="radio" name="soSao" value="{{ $i }}" class="hidden" {{ old('soSao', 5) == $i ? 'checked' : '' }}>
                                    <i class="bi bi-star-fill text-2xl"></i>
                                </label>
                            @endfor
                            <span id="starRatingText" class="ml-2 text-sm font-medium text-gray-600"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1.5">Nội dung đánh giá</label>
                        <textarea name="noiDung" rows="3" class="bhx-input" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." required></textarea>
                    </div>
                    <button type="submit" class="bhx-btn-primary">
                        <i class="bi bi-send"></i> Gửi đánh giá
                    </button>
                </form>
            @else
                <p class="text-sm text-gray-500 flex items-center gap-2">
                    <i class="bi bi-shield-lock"></i> Chỉ khách hàng đã mua sản phẩm này mới được đánh giá.
                </p>
            @endif
        @else
            <p class="text-sm text-gray-500 flex items-center gap-2">
                <a href="{{ route('login') }}" class="text-bhx-600 font-semibold hover:underline">Đăng nhập</a>
                để gửi đánh giá sản phẩm này.
            </p>
        @endauth
    </div>

    {{-- Danh sách đánh giá --}}
    @forelse($danhGias as $dg)
    <div class="bhx-card p-4 mt-3">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-full bg-bhx-100 text-bhx-700 flex items-center justify-center font-bold">{{ mb_substr($dg->taiKhoan->hoTen ?? 'K', 0, 1) }}</span>
            <div>
                <p class="font-medium text-sm">{{ $dg->taiKhoan->hoTen ?? 'Khách hàng' }}</p>
                <span class="flex items-center gap-0.5 mt-0.5">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi {{ $i <= $dg->soSao ? 'bi-star-fill text-bhx-yellow' : 'bi-star text-gray-300' }} text-sm"></i>
                    @endfor
                </span>
            </div>
        </div>
        <p class="text-gray-600 text-sm mt-3 leading-relaxed">{{ $dg->noiDung }}</p>
    </div>
    @empty
    <div class="bhx-card p-8 mt-3 text-center">
        <p class="text-5xl text-gray-200 mb-3"><i class="bi bi-chat-quote"></i></p>
        <p class="text-gray-500 text-sm">Chưa có đánh giá nào cho sản phẩm này.</p>
    </div>
    @endforelse
    <div class="mt-4">{{ $danhGias->withQueryString()->fragment('danh-gia')->links() }}</div>
</div>

{{-- Sản phẩm liên quan --}}
@if($sanPhamLienQuan->count() > 0)
<div class="mt-10">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">Sản phẩm liên quan</h2>
        <a href="{{ route('home') }}" class="text-xs text-bhx-600 hover:underline font-medium">Xem tất cả <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-2 -mx-4 px-4">
        @foreach($sanPhamLienQuan as $sp)
        <div class="w-44 md:w-56 shrink-0">
            @include('home.partials.product-card', ['product' => $sp])
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Mobile sticky buy bar --}}
@if($sanpham->isAvailable())
<div class="fixed bottom-0 inset-x-0 z-40 md:hidden bg-white border-t border-gray-200 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3">
        <div class="flex-1 min-w-0">
            <p class="text-xl font-extrabold text-bhx-orange leading-none">{{ number_format($sanpham->giaBan, 0, ',', '.') }}đ</p>
            <p class="text-[11px] text-gray-400">/{{ $sanpham->donVi ?? 'kg' }}</p>
        </div>
        <button type="submit" form="addCartForm" class="bhx-btn-orange !py-3 w-full max-w-[200px]">
            <i class="bi bi-cart-plus"></i> Thêm vào giỏ
        </button>
    </div>
</div>
@endif

<div class="h-20 md:hidden"></div>

<script>
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-btn').forEach(function (b) {
                b.classList.add('border-transparent', 'text-gray-500');
                b.classList.remove('border-bhx-500', 'text-bhx-700');
            });
            btn.classList.remove('border-transparent', 'text-gray-500');
            btn.classList.add('border-bhx-500', 'text-bhx-700');
            document.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.add('hidden'); });
            var panel = document.getElementById('tab-' + btn.dataset.tab);
            if (panel) { panel.classList.remove('hidden'); }
        });
    });

    (function () {
        var wrap = document.getElementById('starRating');
        if (!wrap) return;
        var labels = {1: 'Rất tệ', 2: 'Tệ', 3: 'Bình thường', 4: 'Tốt', 5: 'Tuyệt vời'};
        var text = document.getElementById('starRatingText');
        function paint(n) {
            wrap.querySelectorAll('label').forEach(function (lb, idx) {
                lb.querySelector('i').className = 'bi text-2xl ' + (idx < n ? 'bi-star-fill text-bhx-yellow' : 'bi-star text-gray-300');
            });
            if (text) text.textContent = labels[n] || '';
        }
        wrap.querySelectorAll('input[name="soSao"]').forEach(function (radio) {
            radio.addEventListener('change', function () { paint(parseInt(radio.value, 10)); });
            radio.closest('label').addEventListener('mouseenter', function () { paint(parseInt(radio.value, 10)); });
        });
        wrap.addEventListener('mouseleave', function () {
            var cur = wrap.querySelector('input[name="soSao"]:checked');
            paint(cur ? parseInt(cur.value, 10) : 5);
        });
        var init = wrap.querySelector('input[name="soSao"]:checked');
        paint(init ? parseInt(init.value, 10) : 5);
    })();

    function buyNow() {
        var form = document.getElementById('addCartForm');
        if (!form) return;
        var qty = form.querySelector('input[name="soLuong"]').value;
        var fd = new FormData(form);
        fd.set('soLuong', qty);
        fetch(form.action, { method: 'POST', body: fd, credentials: 'same-origin' })
            .then(function (resp) {
                if (resp.redirected && resp.url.indexOf('login') !== -1) {
                    window.location.href = resp.url;
                } else {
                    window.location.href = '/gio-hang';
                }
            })
            .catch(function () { form.submit(); });
    }
</script>
@endsection
