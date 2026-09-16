@extends('layouts.app')
@section('title', 'Thêm sản phẩm')
@section('content')
<h1 class="text-2xl font-bold mb-4">Thêm sản phẩm mới</h1>
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('staff.sanpham.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm *</label>
                <input type="text" name="tenSP" value="{{ old('tenSP') }}" class="w-full border rounded px-3 py-2 @error('tenSP') border-red-500 @enderror" required>
                @error('tenSP') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Danh mục *</label>
                <select name="maDM" class="w-full border rounded px-3 py-2 @error('maDM') border-red-500 @enderror" required>
                    <option value="">-- Chọn --</option>
                    @foreach($danhMucs as $dm)
                    <option value="{{ $dm->maDM }}" {{ old('maDM') == $dm->maDM ? 'selected' : '' }}>{{ $dm->tenDM }}</option>
                    @endforeach
                </select>
                @error('maDM') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nhà cung cấp *</label>
                <select name="maNCC" class="w-full border rounded px-3 py-2 @error('maNCC') border-red-500 @enderror" required>
                    <option value="">-- Chọn --</option>
                    @foreach($nhaCungCaps as $ncc)
                    <option value="{{ $ncc->maNCC }}" {{ old('maNCC') == $ncc->maNCC ? 'selected' : '' }}>{{ $ncc->tenNCC }}</option>
                    @endforeach
                </select>
                @error('maNCC') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán *</label>
                <input type="number" name="giaBan" value="{{ old('giaBan', 0) }}" class="w-full border rounded px-3 py-2 @error('giaBan') border-red-500 @enderror" min="0" required>
                @error('giaBan') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tồn kho *</label>
                <input type="number" name="soLuong" value="{{ old('soLuong', 0) }}" class="w-full border rounded px-3 py-2 @error('soLuong') border-red-500 @enderror" min="0" required>
                @error('soLuong') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị tính *</label>
                <select name="donVi" class="w-full border rounded px-3 py-2 @error('donVi') border-red-500 @enderror" required>
                    @foreach(['kg','quả','bó','gói','chai','hộp','thùng','bịch','cây','củ','cái'] as $dv)
                    <option value="{{ $dv }}" {{ old('donVi', 'kg') == $dv ? 'selected' : '' }}>{{ $dv }}</option>
                    @endforeach
                </select>
                @error('donVi') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái *</label>
                <select name="trangThai" class="w-full border rounded px-3 py-2" required>
                    <option value="{{ \App\Models\SanPham::DANG_BAN }}" {{ old('trangThai') == \App\Models\SanPham::DANG_BAN ? 'selected' : '' }}>Đang bán</option>
                    <option value="{{ \App\Models\SanPham::NGUNG_BAN }}" {{ old('trangThai') == \App\Models\SanPham::NGUNG_BAN ? 'selected' : '' }}>Ngừng bán</option>
                </select>
            </div>
            <div class="mb-3 col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Hình ảnh</label>
                <input type="file" name="hinhAnh" accept="image/*" class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-3 col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <textarea name="moTa" rows="3" class="w-full border rounded px-3 py-2">{{ old('moTa') }}</textarea>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">Thêm</button>
            <a href="{{ route('staff.sanpham.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Hủy</a>
        </div>
    </form>
</div>
@endsection
