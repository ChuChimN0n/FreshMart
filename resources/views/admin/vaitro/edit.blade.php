@extends('layouts.app')
@section('title', 'Sửa vai trò')
@section('content')
<h1 class="text-2xl font-bold mb-4">Sửa vai trò</h1>
<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('admin.vaitro.update', $vaiTro) }}">
        @csrf @method('PUT')
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên vai trò *</label>
            <input type="text" name="tenVT" value="{{ old('tenVT', $vaiTro->tenVT) }}" class="w-full border rounded px-3 py-2 @error('tenVT') border-red-500 @enderror" required>
            @error('tenVT') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-3">
            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
            <input type="text" name="moTa" value="{{ old('moTa', $vaiTro->moTa) }}" class="w-full border rounded px-3 py-2">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quyền</label>
            @if($vaiTro->maVT === \App\Models\VaiTro::ADMIN_ID)
                <p class="text-sm text-gray-600">Quản lý có toàn bộ quyền quản trị và kế thừa chức năng nhân viên.</p>
            @else
            <label class="block text-sm font-medium text-gray-700 mb-2">Quyền * (một vai trò chỉ thuộc một nhóm)</label>
            @php
                $customerQuyens = $quyens->whereIn('tenQuyen', \App\Models\Quyen::CUSTOMER_PERMISSIONS);
                $managementQuyens = $quyens->whereNotIn('tenQuyen', \App\Models\Quyen::CUSTOMER_PERMISSIONS);
                $currentQuyens = old('quyens', $vaiTro->quyens->pluck('maQuyen')->toArray());
            @endphp
            <p class="text-sm font-medium text-gray-700 mt-2 mb-1">Nhóm khách hàng</p>
            <div class="grid grid-cols-2 gap-2 mb-2" data-permission-group="customer">
                @foreach($customerQuyens as $q)
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="quyens[]" value="{{ $q->maQuyen }}" {{ in_array($q->maQuyen, $currentQuyens) ? 'checked' : '' }}>
                    <span class="text-sm">{{ $q->tenQuyen }}</span>
                </label>
                @endforeach
            </div>
            <p class="text-sm font-medium text-gray-700 mt-2 mb-1">Nhóm quản trị & nhân viên</p>
            <div class="grid grid-cols-2 gap-2" data-permission-group="management">
                @foreach($managementQuyens as $q)
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="quyens[]" value="{{ $q->maQuyen }}" {{ in_array($q->maQuyen, $currentQuyens) ? 'checked' : '' }}>
                    <span class="text-sm">{{ $q->tenQuyen }}</span>
                </label>
                @endforeach
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
            @endif
            @error('quyens') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            @error('quyens.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">Cập nhật</button>
            <a href="{{ route('admin.vaitro.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Hủy</a>
        </div>
    </form>
</div>
@endsection
