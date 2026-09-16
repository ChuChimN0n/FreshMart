@extends('layouts.app')
@section('title', 'Sửa sản phẩm')
@section('content')
<h1 class="text-2xl font-bold mb-4">Sửa sản phẩm</h1>
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('staff.sanpham.update', $sanPham) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm *</label>
                <input type="text" name="tenSP" value="{{ old('tenSP', $sanPham->tenSP) }}" class="w-full border rounded px-3 py-2 @error('tenSP') border-red-500 @enderror" required>
                @error('tenSP') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục *</label>
                <select name="maDM" class="w-full border rounded px-3 py-2" required>
                    @foreach($danhMucs as $dm)
                    <option value="{{ $dm->maDM }}" {{ old('maDM', $sanPham->maDM) == $dm->maDM ? 'selected' : '' }}>{{ $dm->tenDM }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nhà cung cấp *</label>
                <select name="maNCC" class="w-full border rounded px-3 py-2" required>
                    @foreach($nhaCungCaps as $ncc)
                    <option value="{{ $ncc->maNCC }}" {{ old('maNCC', $sanPham->maNCC) == $ncc->maNCC ? 'selected' : '' }}>{{ $ncc->tenNCC }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán *</label>
                <input type="number" name="giaBan" value="{{ old('giaBan', $sanPham->giaBan) }}" class="w-full border rounded px-3 py-2" min="0" required>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tồn kho *</label>
                <input type="number" name="soLuong" value="{{ old('soLuong', $sanPham->soLuong) }}" class="w-full border rounded px-3 py-2" min="0" required>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị tính *</label>
                <select name="donVi" class="w-full border rounded px-3 py-2" required>
                    @foreach(['kg','quả','bó','gói','chai','hộp','thùng','bịch','cây','củ','cái'] as $dv)
                    <option value="{{ $dv }}" {{ old('donVi', $sanPham->donVi ?? 'kg') == $dv ? 'selected' : '' }}>{{ $dv }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái *</label>
                <select name="trangThai" class="w-full border rounded px-3 py-2" required>
                    <option value="{{ \App\Models\SanPham::DANG_BAN }}" {{ old('trangThai', $sanPham->trangThai) == \App\Models\SanPham::DANG_BAN ? 'selected' : '' }}>Đang bán</option>
                    <option value="{{ \App\Models\SanPham::NGUNG_BAN }}" {{ old('trangThai', $sanPham->trangThai) == \App\Models\SanPham::NGUNG_BAN ? 'selected' : '' }}>Ngừng bán</option>
                </select>
            </div>
            <div class="mb-3 col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh (để trống nếu không đổi)</label>
                @if($sanPham->hinhAnh)
                <div class="mb-2"><img src="{{ asset('storage/'.$sanPham->hinhAnh) }}" class="h-20 rounded"></div>
                @endif
                <input type="file" name="hinhAnh" accept="image/*" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-3 col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea name="moTa" rows="3" class="w-full border rounded px-3 py-2">{{ old('moTa', $sanPham->moTa) }}</textarea>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">Cập nhật</button>
            <a href="{{ route('staff.sanpham.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Hủy</a>
        </div>
    </form>
</div>
@endsection
