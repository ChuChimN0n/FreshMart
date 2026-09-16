@extends('layouts.app')
@section('title', 'Bách Hóa Xanh – Thực phẩm tươi ngon mỗi ngày')
@section('content')

{{-- ===== Hero ===== --}}
<section class="bg-gradient-to-r from-bhx-600 to-bhx-800 rounded-2xl text-white p-8 md:p-12 mb-6 relative overflow-hidden shadow-md">
    <div class="absolute -right-6 -bottom-10 text-[160px] md:text-[220px] leading-none opacity-10 rotate-12">
        <i class="bi bi-basket2-fill"></i>
    </div>
    <div class="relative z-10">
        <span class="inline-flex items-center gap-1.5 bg-bhx-yellow text-bhx-900 text-xs font-bold px-3 py-1 rounded-full mb-4">
            <i class="bi bi-stars"></i> Ưu đãi hằng ngày
        </span>
        <h1 class="text-2xl md:text-4xl font-extrabold mb-3">Bách Hóa Xanh</h1>
        <p class="text-bhx-100 text-base md:text-lg mb-6 max-w-xl">Thực phẩm tươi ngon, giá tốt mỗi ngày. Giao hàng nhanh chóng đến tận nhà bạn.</p>
        <div class="flex gap-3 flex-wrap">
            <a href="#san-pham" class="bhx-btn-orange">
                <i class="bi bi-bag-check"></i> Mua sắm ngay
            </a>
            <a href="#san-pham" class="bhx-btn-outline">
                <i class="bi bi-fire"></i> Xem khuyến mãi
            </a>
        </div>
    </div>
</section>

{{-- ===== Feature strip ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8">
    <div class="bhx-card p-4 flex items-center gap-3">
        <span class="w-11 h-11 rounded-full bg-bhx-100 text-bhx-600 flex items-center justify-center text-xl shrink-0"><i class="bi bi-truck"></i></span>
        <div>
            <p class="font-semibold text-sm">Giao hàng nhanh</p>
            <p class="text-xs text-gray-500 mt-0.5">Nhận hàng tận nơi trong ngày</p>
        </div>
    </div>
    <div class="bhx-card p-4 flex items-center gap-3">
        <span class="w-11 h-11 rounded-full bg-bhx-100 text-bhx-600 flex items-center justify-center text-xl shrink-0"><i class="bi bi-arrow-repeat"></i></span>
        <div>
            <p class="font-semibold text-sm">Đổi trả dễ dàng</p>
            <p class="text-xs text-gray-500 mt-0.5">Hỗ trợ đổi trả khi không hài lòng</p>
        </div>
    </div>
    <div class="bhx-card p-4 flex items-center gap-3">
        <span class="w-11 h-11 rounded-full bg-bhx-100 text-bhx-600 flex items-center justify-center text-xl shrink-0"><i class="bi bi-shield-check"></i></span>
        <div>
            <p class="font-semibold text-sm">Thanh toán an toàn</p>
            <p class="text-xs text-gray-500 mt-0.5">Nhiều hình thức thanh toán tiện lợi</p>
        </div>
    </div>
</div>

{{-- ===== Content: Sidebar + Products ===== --}}
<div id="san-pham" class="grid lg:grid-cols-[240px_1fr] gap-6 items-start">

    {{-- Sidebar danh mục (desktop) --}}
    <aside class="hidden lg:block bhx-card overflow-hidden sticky top-36">
        <div class="bg-bhx-600 text-white px-4 py-3 flex items-center gap-2 text-sm font-bold uppercase tracking-wide">
            <i class="bi bi-grid-3x3-gap-fill"></i> Danh mục sản phẩm
        </div>
        <div class="p-2">
            <a href="{{ route('home') }}"
               class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-sm transition {{ !request('maDM') ? 'bg-bhx-50 text-bhx-700 font-semibold' : 'text-gray-700 hover:bg-bhx-50' }}">
                <i class="bi bi-collection"></i> Tất cả sản phẩm
            </a>
            @foreach($danhMucs as $dm)
            <a href="{{ route('home', ['maDM' => $dm->maDM]) }}"
               class="flex items-center justify-between px-3 py-2.5 rounded-lg text-sm transition {{ request('maDM') == $dm->maDM ? 'bg-bhx-50 text-bhx-700 font-semibold' : 'text-gray-700 hover:bg-bhx-50' }}">
                {{ $dm->tenDM }}
                @if(request('maDM') == $dm->maDM)
                    <i class="bi bi-check2 text-bhx-500"></i>
                @endif
            </a>
            @endforeach
        </div>
    </aside>

    <div>
        {{-- Filter bar --}}
        <form method="GET" class="bhx-card p-3 mb-4 flex gap-2">
            <div class="flex-1 relative">
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm sản phẩm..."
                       class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:border-bhx-500 focus:outline-none focus:ring-2 focus:ring-bhx-500/20">
            </div>
            <select name="maDM" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-bhx-500 focus:outline-none">
                <option value="">-- Danh mục --</option>
                @foreach($danhMucs as $dm)
                <option value="{{ $dm->maDM }}" {{ request('maDM') == $dm->maDM ? 'selected' : '' }}>{{ $dm->tenDM }}</option>
                @endforeach
            </select>
            <button type="submit" class="bhx-btn-orange !py-2">
                <i class="bi bi-funnel"></i> Lọc
            </button>
        </form>

        {{-- Danh mục chips (mobile) --}}
        <div class="lg:hidden flex gap-2 overflow-x-auto pb-2 mb-4 -mx-4 px-4">
            <a href="{{ route('home') }}"
               class="shrink-0 px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap {{ !request('maDM') ? 'bg-bhx-500 text-white' : 'bg-white text-gray-600 shadow-sm' }}">
                Tất cả
            </a>
            @foreach($danhMucs as $dm)
            <a href="{{ route('home', ['maDM' => $dm->maDM]) }}"
               class="shrink-0 px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap {{ request('maDM') == $dm->maDM ? 'bg-bhx-500 text-white' : 'bg-white text-gray-600 shadow-sm' }}">
                {{ $dm->tenDM }}
            </a>
            @endforeach
        </div>

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">
                Danh sách sản phẩm
                <span class="text-sm font-normal text-gray-400">({{ $sanPhams->total() }})</span>
            </h2>
            @if(request('search') || request('maDM'))
                <a href="{{ route('home') }}" class="text-xs text-bhx-600 hover:underline font-medium">Xem tất cả <i class="bi bi-arrow-right"></i></a>
            @endif
        </div>

        {{-- Product grid --}}
        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
            @forelse($sanPhams as $sp)
            @include('home.partials.product-card', ['product' => $sp])
            @empty
            <div class="col-span-2 md:col-span-3 xl:col-span-4 text-center py-16">
                <div class="text-6xl mb-4 text-gray-300"><i class="bi bi-search"></i></div>
                <p class="text-gray-500 text-lg">Không tìm thấy sản phẩm nào</p>
                <a href="{{ route('home') }}" class="text-bhx-600 hover:underline mt-2 inline-block font-medium">Xem tất cả sản phẩm</a>
            </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $sanPhams->withQueryString()->links() }}</div>
    </div>
</div>
@endsection