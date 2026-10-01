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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
<div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5">
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
<aside class="bg-white rounded-xl shadow-sm p-5 lg:sticky lg:top-5">
    <h2 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
        <span class="w-7 h-7 rounded-lg bg-bhx-50 text-bhx-600 flex items-center justify-center"><i class="bi bi-eye"></i></span>
        Xem trước hiển thị
    </h2>
    <p class="text-sm text-gray-500 mb-4">Cách danh mục hiện ở trang danh sách.</p>
    <div class="rounded-lg bg-gray-50 p-4 text-center">
        <span id="ttBadge" class="bhx-tag bg-gray-100 text-gray-600">Tên danh mục...</span>
    </div>
    <p class="text-xs text-gray-400 mt-4">Nên đặt ngắn gọn, không trùng danh mục có sẵn.</p>
</aside>
</div>

<script>
    (function () {
        var input = document.querySelector('input[name=tenDM]');
        var badge = document.getElementById('ttBadge');
        function update() {
            badge.textContent = (input.value.trim()) || 'Tên danh mục...';
        }
        input.addEventListener('input', update);
        update();
    })();
</script>
@endsection
