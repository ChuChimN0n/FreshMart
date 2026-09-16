@extends('layouts.app')
@section('title', 'Quản lý đơn hàng')
@section('content')
<h1 class="text-2xl font-bold mb-4">Quản lý đơn hàng</h1>

<form method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm đơn hàng..." class="border rounded px-3 py-2 flex-1">
    <select name="trangThai" class="border rounded px-3 py-2">
        <option value="">-- Tất cả --</option>
        @foreach(\App\Models\DonHang::TRANG_THAI as $key => $val)
        <option value="{{ $key }}" {{ request('trangThai') == $key ? 'selected' : '' }}>{{ $val }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tìm</button>
</form>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-3 text-left">Mã đơn</th>
                <th class="px-4 py-3 text-left">Khách hàng</th>
                <th class="px-4 py-3 text-left">Người nhận</th>
                <th class="px-4 py-3 text-left">Ngày đặt</th>
                <th class="px-4 py-3 text-right">Tổng tiền</th>
                <th class="px-4 py-3 text-left">Trạng thái</th>
                <th class="px-4 py-3 text-left">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donHangs as $dh)
            <tr class="border-t">
                <td class="px-4 py-3 font-medium">#{{ $dh->maDH }}</td>
                <td class="px-4 py-3">{{ $dh->taiKhoan->hoTen ?? '' }}</td>
                <td class="px-4 py-3">{{ $dh->tenNguoiNhan }}</td>
                <td class="px-4 py-3">{{ $dh->ngayDat->format('d/m/Y') }}</td>
                <td class="px-4 py-3 text-right">{{ number_format($dh->tongTien, 0, ',', '.') }}đ</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 rounded text-xs font-medium {{ $dh->trangThaiBadge }}">
                        {{ $dh->trangThaiLabel }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <a href="{{ route('staff.donhang.detail', $dh) }}" class="text-blue-600 hover:underline">Chi tiết</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Không có đơn hàng nào</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $donHangs->withQueryString()->links() }}</div>
@endsection
