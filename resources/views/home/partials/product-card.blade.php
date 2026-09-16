<div class="bhx-card overflow-hidden group hover:shadow-lg transition-shadow duration-300 flex flex-col shrink-0">
    <a href="{{ route('home.show', $product) }}" class="block relative aspect-square bg-bhx-50 overflow-hidden">
        @if($product->hinhAnh)
            <img src="{{ asset('storage/'.$product->hinhAnh) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $product->tenSP }}">
        @else
            <div class="w-full h-full flex flex-col items-center justify-center text-bhx-300">
                <i class="bi bi-basket text-5xl"></i>
            </div>
        @endif
        @if($product->soLuong <= 5 && $product->soLuong > 0)
            <span class="absolute top-2 left-2 bg-bhx-red text-white text-[11px] px-2 py-0.5 rounded-full font-semibold">Sắp hết hàng</span>
        @elseif($product->soLuong <= 0)
            <span class="absolute top-2 left-2 bg-gray-700 text-white text-[11px] px-2 py-0.5 rounded-full font-semibold">Hết hàng</span>
        @endif
    </a>
    <div class="p-3 flex flex-col flex-1">
        <span class="text-[11px] text-gray-400 uppercase tracking-wide mb-1">{{ $product->danhMuc->tenDM ?? 'Sản phẩm' }}</span>
        <a href="{{ route('home.show', $product) }}" class="text-sm font-medium text-gray-800 line-clamp-2 hover:text-bhx-600 mb-2">{{ $product->tenSP }}</a>
        <div class="mt-auto flex items-end justify-between pt-1">
            <div>
                <span class="bhx-price text-base md:text-lg leading-none">{{ number_format($product->giaBan, 0, ',', '.') }}đ</span>
                <span class="text-[11px] text-gray-400">/{{ $product->donVi ?? 'kg' }}</span>
            </div>
            @auth
            <form method="POST" action="{{ route('giohang.add') }}">
                @csrf
                <input type="hidden" name="maSP" value="{{ $product->maSP }}">
                <input type="hidden" name="soLuong" value="1">
                <button type="submit" class="w-9 h-9 rounded-full bg-bhx-500 hover:bg-bhx-600 active:bg-bhx-700 text-white flex items-center justify-center transition shadow-sm" aria-label="Thêm vào giỏ">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </form>
            @else
            <a href="{{ route('login') }}" class="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition" title="Đăng nhập để mua">
                <i class="bi bi-cart-plus"></i>
            </a>
            @endauth
        </div>
    </div>
</div>