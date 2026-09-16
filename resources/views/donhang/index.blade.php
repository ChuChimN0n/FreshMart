@extends('layouts.app')
@section('title', 'Đơn hàng của tôi')
@section('content')
<h1 class="text-2xl font-bold mb-4 flex items-center gap-2">
    <i class="bi bi-receipt text-bhx-500"></i> Đơn hàng của tôi
</h1>

<div class="bhx-card overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-bhx-600 text-white">
            <tr>
                <th class="px-4 py-3 text-left">Mã đơn</th>
                <th class="px-4 py-3 text-left">Ngày đặt</th>
                <th class="px-4 py-3 text-right">Tổng tiền</th>
                <th class="px-4 py-3 text-left">Trạng thái</th>
                <th class="px-4 py-3 text-left">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donHangs as $dh)
            <tr class="border-t hover:bg-bhx-50/50">
                <td class="px-4 py-3 font-semibold text-bhx-700">#{{ $dh->maDH }}</td>
                <td class="px-4 py-3">{{ $dh->ngayDat->format('d/m/Y H:i') }}</td>
                <td class="px-4 py-3 text-right bhx-price">{{ number_format($dh->tongTien, 0, ',', '.') }}đ</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $dh->trangThaiBadge }}">
                        {{ $dh->trangThaiLabel }}
                    </span>
                </td>
                <td class="px-4 py-3 flex gap-4">
                    <a href="{{ route('donhang.detail', $dh) }}" class="text-bhx-600 hover:underline font-medium"><i class="bi bi-eye"></i> Chi tiết</a>
                    @if($dh->canCancel())
                    <form method="POST" action="{{ route('donhang.cancel', $dh) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-bhx-red hover:underline" onclick="return confirm('Bạn muốn hủy đơn này?')"><i class="bi bi-x-circle"></i> Hủy</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-12 text-center text-gray-500">Chưa có đơn hàng nào</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $donHangs->links() }}</div>
@endsection