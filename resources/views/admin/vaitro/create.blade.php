@extends('layouts.management')
@section('title', 'Thêm vai trò')
@section('content')
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('admin.vaitro.index') }}" class="hover:text-bhx-600 transition">Vai trò & quyền</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">Thêm mới</span>
</nav>

<div class="mb-5">
    <h1 class="text-2xl font-bold text-gray-800">Thêm vai trò mới</h1>
    <p class="text-sm text-gray-500 mt-1">Một vai trò chỉ thuộc một nhóm quyền</p>
</div>

<div class="bg-white rounded-xl shadow-sm p-5 max-w-2xl">
    <form method="POST" action="{{ route('admin.vaitro.store') }}">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tên vai trò <span class="text-red-500">*</span></label>
                <input type="text" name="tenVT" value="{{ old('tenVT') }}" placeholder="VD: Nhân viên kho" class="bhx-input @error('tenVT') !border-red-500 @enderror" required>
                @error('tenVT') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                <input type="text" name="moTa" value="{{ old('moTa') }}" placeholder="VD: Quản lý kho hàng" class="bhx-input">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quyền <span class="text-red-500">*</span></label>
            @php
                $customerQuyens = $quyens->whereIn('tenQuyen', \App\Models\Quyen::CUSTOMER_PERMISSIONS);
                $managementQuyens = $quyens->whereNotIn('tenQuyen', \App\Models\Quyen::CUSTOMER_PERMISSIONS);
                $checkedQuyens = old('quyens', []);
            @endphp
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Nhóm khách hàng</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mb-4" data-permission-group="customer">
                @foreach($customerQuyens as $q)
                    <label class="flex items-center gap-2.5 p-2.5 rounded-lg border border-gray-200 cursor-pointer hover:border-bhx-400 has-checked:border-bhx-500 has-checked:bg-bhx-50/50 transition text-sm text-gray-700">
                        <input type="checkbox" name="quyens[]" value="{{ $q->maQuyen }}" {{ in_array($q->maQuyen, $checkedQuyens) ? 'checked' : '' }} class="accent-green-600">
                        {{ $q->tenQuyen }}
                    </label>
                @endforeach
            </div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Nhóm quản trị & nhân viên</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2" data-permission-group="management">
                @foreach($managementQuyens as $q)
                    <label class="flex items-center gap-2.5 p-2.5 rounded-lg border border-gray-200 cursor-pointer hover:border-bhx-400 has-checked:border-bhx-500 has-checked:bg-bhx-50/50 transition text-sm text-gray-700">
                        <input type="checkbox" name="quyens[]" value="{{ $q->maQuyen }}" {{ in_array($q->maQuyen, $checkedQuyens) ? 'checked' : '' }} class="accent-green-600">
                        {{ $q->tenQuyen }}
                    </label>
                @endforeach
            </div>
            @error('quyens') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            @error('quyens.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <script>
            document.querySelectorAll('[data-permission-group] input[type=checkbox]').forEach(function (cb) {
                cb.addEventListener('change', function () {
                    if (!cb.checked) return;
                    var group = cb.closest('[data-permission-group]').dataset.permissionGroup;
                    document.querySelectorAll('[data-permission-group]:not([data-permission-group="' + group + '"]) input[type=checkbox]').forEach(function (other) {
                        other.checked = false;
                    });
                });
            });
        </script>
        <div class="flex gap-2">
            <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Thêm</button>
            <a href="{{ route('admin.vaitro.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Hủy</a>
        </div>
    </form>
</div>
@endsection
