@extends('layouts.management')
@section('title', 'Thêm NCC')
@section('content')
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('admin.nhacungcap.index') }}" class="hover:text-bhx-600 transition">Nhà cung cấp</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">Thêm mới</span>
</nav>

<div class="mb-5">
    <h1 class="text-2xl font-bold text-gray-800">Thêm nhà cung cấp</h1>
    <p class="text-sm text-gray-500 mt-1">Thông tin liên hệ để nhập hàng và đối soát</p>
</div>

<div class="bg-white rounded-xl shadow-sm p-5 max-w-xl">
    <form method="POST" action="{{ route('admin.nhacungcap.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên NCC <span class="text-red-500">*</span></label>
            <input type="text" name="tenNCC" value="{{ old('tenNCC') }}" placeholder="VD: Nông trại Đà Lạt" class="bhx-input @error('tenNCC') !border-red-500 @enderror" required>
            @error('tenNCC') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mã NCC <i class="bi bi-lock-fill text-xs text-gray-400" title="Mã do hệ thống tự sinh"></i></label>
            <input type="text" id="codeNCCInput" readonly placeholder="Hệ thống tự sinh khi lưu (VD: NCC-0007)"
                   class="bhx-input font-mono bg-gray-50 text-gray-600">
            <p class="text-xs text-gray-400 mt-1">Mã do hệ thống tự sinh, không chỉnh sửa. Mã chốt khi bấm Lưu.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                <input type="text" name="soDienThoai" value="{{ old('soDienThoai') }}" placeholder="VD: 0912345678" class="bhx-input @error('soDienThoai') !border-red-500 @enderror" required>
                @error('soDienThoai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="VD: lienhe@ncc.vn" class="bhx-input @error('email') !border-red-500 @enderror">
                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
            <input type="text" name="diaChi" value="{{ old('diaChi') }}" placeholder="VD: Đà Lạt, Lâm Đồng" class="bhx-input">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Thêm</button>
            <a href="{{ route('admin.nhacungcap.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Hủy</a>
        </div>
    </form>
</div>

<script>
    (function () {
        const codeInput = document.getElementById('codeNCCInput');
        fetch("{{ route('admin.suggest.code') }}?profile=nhacungcap", { headers: { 'Accept': 'application/json' } })
            .then((res) => (res.ok ? res.json() : null))
            .then((data) => { if (data && data.code) codeInput.value = data.code; })
            .catch(() => {});
    })();
</script>
@endsection
