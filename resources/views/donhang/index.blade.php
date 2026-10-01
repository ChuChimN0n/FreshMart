@extends('layouts.app')
@section('title', 'Đơn hàng của tôi')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold flex items-center gap-2">
        <i class="bi bi-receipt text-bhx-500"></i> Đơn hàng của tôi
    </h1>
    <a href="{{ route('giohang.index') }}" class="bhx-btn-orange !px-4 !py-2">
        Đặt hàng <i class="bi bi-arrow-right"></i>
    </a>
</div>

<div class="bhx-card overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead class="bg-gray-50">
            <tr class="text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3 text-left font-semibold">Mã đơn</th>
                <th class="px-4 py-3 text-left font-semibold">Ngày đặt</th>
                <th class="px-4 py-3 text-right font-semibold">Tổng tiền</th>
                <th class="px-4 py-3 text-left font-semibold">Trạng thái</th>
                <th class="px-4 py-3 text-right font-semibold">Hành động</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($donHangs as $dh)
            <tr class="hover:bg-bhx-50/50 transition">
                <td class="px-4 py-3 font-semibold text-bhx-700 whitespace-nowrap">{{ $dh->maDon ?: '#'.$dh->maDH }}</td>
                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $dh->ngayDat->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3 text-right bhx-price whitespace-nowrap">{{ number_format($dh->tongTien, 0, ',', '.') }}đ</td>
                <td class="px-4 py-3">
                    <span class="bhx-tag {{ $dh->trangThaiBadge }}">
                        {{ $dh->trangThaiLabel }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('donhang.detail', $dh) }}" class="text-bhx-600 hover:underline font-medium whitespace-nowrap"><i class="bi bi-eye"></i> Chi tiết</a>
                    <form method="POST" action="{{ route('donhang.cancel', $dh) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-bhx-red hover:underline whitespace-nowrap" onclick="return confirm('Bạn muốn hủy đơn này?')"><i class="bi bi-x-circle"></i> Hủy</button>
                    </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-12 text-center">
                <div class="text-5xl mb-3 text-gray-300"><i class="bi bi-receipt"></i></div>
                <p class="text-gray-500 font-medium">Chưa có đơn hàng nào</p>
                <a href="{{ route('home') }}" class="bhx-btn-primary text-sm mt-4">Mua sắm ngay</a>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
<div class="mt-4">{{ $donHangs->links() }}</div>
@endsection