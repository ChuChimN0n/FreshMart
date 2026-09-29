@extends('layouts.management')
@section('title', 'Phiếu ' . $phieuNhap->maPhieu)
@section('content')

<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('staff.nhaphang.index') }}" class="hover:text-bhx-600 transition">Nhập hàng</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">{{ $phieuNhap->maPhieu }}</span>
</nav>

<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Phiếu {{ $phieuNhap->maPhieu }}</h1>
        <p class="text-sm text-gray-500 mt-1">
            Tạo lúc {{ $phieuNhap->ngayTao->format('H:i d/m/Y') }} · {{ $phieuNhap->nguoiTao->hoTen ?? '—' }}
            @if($phieuNhap->ngayXacNhan)
                · Xác nhận {{ $phieuNhap->ngayXacNhan->format('H:i d/m/Y') }}
            @endif
        </p>
    </div>
    <div class="flex items-center gap-3">
        <span class="bhx-tag text-sm px-3 py-1.5 {{ $phieuNhap->trangThaiBadge }}">{{ $phieuNhap->trangThaiLabel }}</span>
        @if($phieuNhap->isNhap())
            <form method="POST" action="{{ route('staff.nhaphang.confirm', $phieuNhap) }}" onsubmit="return confirm('Xác nhận nhập kho? Tồn kho sẽ tăng và không thể hoàn tác.')">
                @csrf
                @method('PATCH')
                <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Xác nhận nhập kho</button>
            </form>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-5 items-start">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-bold text-gray-800 mb-4">Thông tin phiếu</h2>
        <dl class="text-sm space-y-3">
            <div class="flex justify-between gap-3"><dt class="text-gray-500">Nhà cung cấp</dt><dd class="font-medium text-right">{{ $phieuNhap->nhaCungCap->tenNCC ?? '—' }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-gray-500">SĐT NCC</dt><dd class="font-medium">{{ $phieuNhap->nhaCungCap->soDienThoai ?? '—' }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-gray-500">Người tạo</dt><dd class="font-medium">{{ $phieuNhap->nguoiTao->hoTen ?? '—' }}</dd></div>
            <div class="flex justify-between gap-3"><dt class="text-gray-500">Ghi chú</dt><dd class="text-right">{{ $phieuNhap->ghiChu ?: '—' }}</dd></div>
        </dl>
    </div>
    <div class="lg:col-span-3 bg-white rounded-xl shadow-sm p-5">
        <h2 class="font-bold text-gray-800 mb-4">Chi tiết hàng nhập</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs uppercase text-gray-500 border-b">
                        <th class="py-2 pr-2">Sản phẩm</th>
                        <th class="py-2 pr-2 text-right">Số lượng</th>
                        <th class="py-2 pr-2 text-right">Giá nhập</th>
                        <th class="py-2 text-right">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($phieuNhap->chiTiets as $ct)
                        <tr class="border-b last:border-0">
                            <td class="py-2 pr-2 font-medium">{{ $ct->sanPham->tenSP ?? '#'.$ct->maSP }}</td>
                            <td class="py-2 pr-2 text-right">{{ $ct->soLuong }}</td>
                            <td class="py-2 pr-2 text-right"><span class="bhx-price">{{ number_format($ct->giaNhap, 0, ',', '.') }}đ</span></td>
                            <td class="py-2 text-right font-semibold"><span class="bhx-price">{{ number_format($ct->thanhTien, 0, ',', '.') }}đ</span></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2">
                        <td colspan="3" class="py-3 font-bold text-right">Tổng cộng</td>
                        <td class="py-3 text-right font-bold text-bhx-700 text-lg"><span class="bhx-price">{{ number_format($phieuNhap->tongTien, 0, ',', '.') }}đ</span></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
