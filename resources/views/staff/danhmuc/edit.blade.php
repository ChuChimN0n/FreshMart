@extends('layouts.management')
@section('title', 'Sửa danh mục')
@section('content')
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('staff.danhmuc.index') }}" class="hover:text-bhx-600 transition">Quản lý danh mục</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">{{ $danhMuc->tenDM }}</span>
</nav>

<div class="mb-5">
    <h1 class="text-2xl font-bold text-gray-800">Sửa danh mục</h1>
    <p class="text-sm text-gray-500 mt-1">Mã #{{ $danhMuc->maDM }}</p>
</div>

<div class="bg-white rounded-xl shadow-sm p-5 max-w-xl">
    <form method="POST" action="{{ route('staff.danhmuc.update', $danhMuc) }}">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục <span class="text-red-500">*</span></label>
            <input type="text" name="tenDM" value="{{ old('tenDM', $danhMuc->tenDM) }}" class="bhx-input @error('tenDM') !border-red-500 @enderror" required>
            @error('tenDM') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Cập nhật</button>
            <a href="{{ route('staff.danhmuc.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Hủy</a>
        </div>
    </form>
</div>
@endsection
