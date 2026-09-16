@extends('layouts.app')
@section('title', 'Chi tiết đơn #' . $donHang->maDH)
@section('content')
<a href="{{ route('staff.donhang.index') }}" class="text-bhx-600 hover:underline mb-4 inline-block">← Quay lại danh sách</a>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-4">
        <h2 class="font-bold mb-3">Thông tin đơn hàng</h2>
        <p><strong>Mã đơn:</strong> #{{ $donHang->maDH }}</p>
        <p><strong>Khách hàng:</strong> {{ $donHang->taiKhoan->hoTen ?? '' }}</p>
        <p><strong>Ngày đặt:</strong> {{ $donHang->ngayDat->format('d/m/Y H:i') }}</p>
        <p><strong>Trạng thái:</strong>
            <span class="px-2 py-1 rounded text-xs font-medium {{ $donHang->trangThaiBadge }}">
                {{ $donHang->trangThaiLabel }}
            </span>
        </p>
        <hr class="my-3">
        <p><strong>Người nhận:</strong> {{ $donHang->tenNguoiNhan }}</p>
        <p><strong>Số ĐT:</strong> {{ $donHang->soDienThoai }}</p>
        <p><strong>Địa chỉ:</strong> {{ $donHang->diaChi }}</p>
        <p class="mt-3 text-lg font-bold text-bhx-600">Tổng: {{ number_format($donHang->tongTien, 0, ',', '.') }}đ</p>
    </div>

    <div>
        <div class="bg-white rounded-lg shadow p-4">
            <h2 class="font-bold mb-3">Chi tiết sản phẩm</h2>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left">Sản phẩm</th>
                        <th class="px-3 py-2 text-right">Đơn giá</th>
                        <th class="px-3 py-2 text-center">SL</th>
                        <th class="px-3 py-2 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donHang->chiTietDonHangs as $ct)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $ct->sanPham->tenSP ?? 'SP đã xóa' }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($ct->donGia, 0, ',', '.') }}đ</td>
                        <td class="px-3 py-2 text-center">{{ $ct->soLuong }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($ct->thanhTien, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($donHang->trangThai != \App\Models\DonHang::HOAN_THANH && $donHang->trangThai != \App\Models\DonHang::DA_HUY)
        <div class="bg-white rounded-lg shadow p-4 mt-4">
            <h2 class="font-bold mb-3">Cập nhật trạng thái</h2>
            <form method="POST" action="{{ route('staff.donhang.update', $donHang) }}">
                @csrf @method('PATCH')
                <select name="trangThai" class="w-full border rounded px-3 py-2 mb-3">
                    @foreach(\App\Models\DonHang::VALID_TRANSITIONS[$donHang->trangThai] ?? [] as $next)
                    <option value="{{ $next }}">{{ \App\Models\DonHang::TRANG_THAI[$next] }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cập nhật</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
