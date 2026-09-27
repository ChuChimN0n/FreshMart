@extends('layouts.management')
@section('title', 'Thêm danh mục')
@section('content')
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('staff.danhmuc.index') }}" class="hover:text-bhx-600 transition">Quản lý danh mục</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">Thêm mới</span>
</nav>

<div class="mb-5">
    <h1 class="text-2xl font-bold text-gray-800">Thêm danh mục mới</h1>
    <p class="text-sm text-gray-500 mt-1">Danh mục giúp nhóm các sản phẩm cùng loại</p>
</div>

<div class="bg-white rounded-xl shadow-sm p-5 max-w-xl">
    <form method="POST" action="{{ route('staff.danhmuc.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục <span class="text-red-500">*</span></label>
            <input type="text" name="tenDM" value="{{ old('tenDM') }}" placeholder="VD: Rau củ quả" class="bhx-input @error('tenDM') !border-red-500 @enderror" required>
            @error('tenDM') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Thêm</button>
            <a href="{{ route('staff.danhmuc.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Hủy</a>
        </div>
    </form>
</div>
@endsection
