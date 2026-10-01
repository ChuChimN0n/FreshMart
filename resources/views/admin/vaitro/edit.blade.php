@extends('layouts.management')
@section('title', 'Sửa vai trò')
@section('content')
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('admin.vaitro.index') }}" class="hover:text-bhx-600 transition">Vai trò & quyền</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">{{ $vaiTro->tenVT }}</span>
</nav>

<div class="mb-5">
    <h1 class="text-2xl font-bold text-gray-800">Sửa vai trò</h1>
    <p class="text-sm text-gray-500 mt-1">{{ $vaiTro->moTa ?: 'Cập nhật tên và quyền của vai trò' }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
<div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5">
    <form method="POST" action="{{ route('admin.vaitro.update', $vaiTro) }}">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="bi bi-tag text-bhx-500"></i> Tên vai trò <span class="text-red-500">*</span></label>
                <input type="text" name="tenVT" value="{{ old('tenVT', $vaiTro->tenVT) }}" class="bhx-input @error('tenVT') !border-red-500 @enderror" required>
                @error('tenVT') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="bi bi-card-text text-bhx-500"></i> Mô tả</label>
                <input type="text" name="moTa" value="{{ old('moTa', $vaiTro->moTa) }}" class="bhx-input">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quyền</label>
            @if($vaiTro->maVT === \App\Models\VaiTro::ADMIN_ID)
                <p class="text-sm text-gray-600 bg-bhx-50 rounded-lg p-3">Quản lý có toàn bộ quyền quản trị và kế thừa chức năng nhân viên.</p>
            @else
            <label class="block text-sm font-medium text-gray-700 mb-2">Quyền <span class="text-red-500">*</span> (một vai trò chỉ thuộc một nhóm)</label>
            @php
                $customerQuyens = $quyens->whereIn('tenQuyen', \App\Models\Quyen::CUSTOMER_PERMISSIONS);
                $managementQuyens = $quyens->whereNotIn('tenQuyen', \App\Models\Quyen::CUSTOMER_PERMISSIONS);
                $currentQuyens = old('quyens', $vaiTro->quyens->pluck('maQuyen')->toArray());
            @endphp
            <div class="rounded-xl border border-gray-200 bg-gray-50/60 p-4 mb-4">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="flex items-center gap-2 text-sm font-bold text-gray-700">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="bi bi-people"></i></span>
                        Nhóm khách hàng
                        <span class="bhx-tag bg-blue-100 text-blue-700" data-count-for="customer">Đã chọn 0/{{ $customerQuyens->count() }}</span>
                    </p>
                    <div class="flex gap-2 text-xs font-medium">
                        <button type="button" class="text-bhx-600 hover:text-bhx-700 hover:underline" data-select-all="customer">Chọn tất cả</button>
                        <span class="text-gray-300">|</span>
                        <button type="button" class="text-gray-500 hover:text-gray-700 hover:underline" data-clear-group="customer">Bỏ chọn</button>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2" data-permission-group="customer">
                    @foreach($customerQuyens as $q)
                        <label class="flex items-center gap-2.5 p-2.5 rounded-lg border border-gray-200 bg-white shadow-sm cursor-pointer hover:border-bhx-400 hover:shadow has-checked:border-bhx-500 has-checked:bg-bhx-50/50 transition text-sm text-gray-700">
                            <input type="checkbox" name="quyens[]" value="{{ $q->maQuyen }}" {{ in_array($q->maQuyen, $currentQuyens) ? 'checked' : '' }} class="accent-green-600">
                            {{ $q->tenQuyen }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gray-50/60 p-4">
                <div class="flex items-center justify-between gap-2 mb-3">
                    <p class="flex items-center gap-2 text-sm font-bold text-gray-700">
                        <span class="w-7 h-7 rounded-lg bg-bhx-50 text-bhx-600 flex items-center justify-center"><i class="bi bi-shield-lock"></i></span>
                        Nhóm quản trị & nhân viên
                        <span class="bhx-tag bg-bhx-100 text-bhx-700" data-count-for="management">Đã chọn 0/{{ $managementQuyens->count() }}</span>
                    </p>
                    <div class="flex gap-2 text-xs font-medium">
                        <button type="button" class="text-bhx-600 hover:text-bhx-700 hover:underline" data-select-all="management">Chọn tất cả</button>
                        <span class="text-gray-300">|</span>
                        <button type="button" class="text-gray-500 hover:text-gray-700 hover:underline" data-clear-group="management">Bỏ chọn</button>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2" data-permission-group="management">
                    @foreach($managementQuyens as $q)
                        <label class="flex items-center gap-2.5 p-2.5 rounded-lg border border-gray-200 bg-white shadow-sm cursor-pointer hover:border-bhx-400 hover:shadow has-checked:border-bhx-500 has-checked:bg-bhx-50/50 transition text-sm text-gray-700">
                            <input type="checkbox" name="quyens[]" value="{{ $q->maQuyen }}" {{ in_array($q->maQuyen, $currentQuyens) ? 'checked' : '' }} class="accent-green-600">
                            {{ $q->tenQuyen }}
                        </label>
                    @endforeach
                </div>
            </div>
            <script>
            function updateGroupCounts() {
                document.querySelectorAll('[data-permission-group]').forEach(function (group) {
                    var boxes = group.querySelectorAll('input[type=checkbox]');
                    var checked = group.querySelectorAll('input[type=checkbox]:checked').length;
                    var badge = document.querySelector('[data-count-for="' + group.dataset.permissionGroup + '"]');
                    if (badge) badge.textContent = 'Đã chọn ' + checked + '/' + boxes.length;
                });
                updateSummary();
            }
            function updateSummary() {
                var ten = document.querySelector('input[name=tenVT]');
                var hasBoxes = document.querySelector('[data-permission-group]');
                document.getElementById('tomTatTen').textContent = (ten && ten.value.trim()) || '—';
                var box = document.getElementById('tomTatQuyen');
                if (!hasBoxes) {
                    box.innerHTML = '<p class="text-gray-500">Quản lý có toàn bộ quyền quản trị và kế thừa chức năng nhân viên.</p>';
                    return;
                }
                var html = '';
                document.querySelectorAll('[data-permission-group]').forEach(function (group) {
                    var title = group.dataset.permissionGroup === 'customer' ? 'Nhóm khách hàng' : 'Nhóm quản trị & nhân viên';
                    var names = [];
                    group.querySelectorAll('input[type=checkbox]:checked').forEach(function (cb) {
                        names.push(cb.closest('label').innerText.trim());
                    });
                    html += '<div><p class="font-medium text-gray-700 mb-1">' + title + ' (' + names.length + ')</p>';
                    html += names.length
                        ? '<ul class="list-disc list-inside text-gray-600 space-y-0.5">' + names.map(function (n) { return '<li>' + n + '</li>'; }).join('') + '</ul>'
                        : '<p class="text-gray-400">Chưa chọn quyền nào</p>';
                    html += '</div>';
                });
                box.innerHTML = html;
            }
                document.querySelectorAll('[data-permission-group] input[type=checkbox]').forEach(function (cb) {
                    cb.addEventListener('change', function () {
                        if (cb.checked) {
                            var group = cb.closest('[data-permission-group]').dataset.permissionGroup;
                            document.querySelectorAll('[data-permission-group]:not([data-permission-group="' + group + '"]) input[type=checkbox]').forEach(function (other) {
                                other.checked = false;
                            });
                        }
                        updateGroupCounts();
                    });
                });
                document.querySelectorAll('[data-select-all]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var group = document.querySelector('[data-permission-group="' + btn.dataset.selectAll + '"]');
                        if (!group) return;
                        group.querySelectorAll('input[type=checkbox]').forEach(function (cb) { cb.checked = true; });
                        group.querySelectorAll('input[type=checkbox]').forEach(function (cb) { cb.dispatchEvent(new Event('change')); });
                    });
                });
                document.querySelectorAll('[data-clear-group]').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        var group = document.querySelector('[data-permission-group="' + btn.dataset.clearGroup + '"]');
                        if (!group) return;
                        group.querySelectorAll('input[type=checkbox]').forEach(function (cb) { cb.checked = false; });
                        updateGroupCounts();
                    });
                });
                var tenInput = document.querySelector('input[name=tenVT]');
                if (tenInput) tenInput.addEventListener('input', updateSummary);
                updateGroupCounts();
            </script>
            @endif
            @error('quyens') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            @error('quyens.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2 mt-6 pt-4 border-t">
            <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Cập nhật</button>
            <a href="{{ route('admin.vaitro.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Hủy</a>
        </div>
    </form>
</div>
<aside class="bg-white rounded-xl shadow-sm p-5 lg:sticky lg:top-5">
    <h2 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
        <span class="w-7 h-7 rounded-lg bg-bhx-50 text-bhx-600 flex items-center justify-center"><i class="bi bi-clipboard-check"></i></span>
        Tóm tắt vai trò
    </h2>
    <p class="text-sm text-gray-500 mb-4">Xem trước quyền sẽ được gán.</p>
    <dl class="text-sm space-y-2 mb-4">
        <div class="flex justify-between gap-2"><dt class="text-gray-500">Tên vai trò</dt><dd id="tomTatTen" class="font-medium text-right">—</dd></div>
    </dl>
    <div id="tomTatQuyen" class="space-y-3 text-sm">
        @if($vaiTro->maVT === \App\Models\VaiTro::ADMIN_ID)
            <p class="text-gray-500">Quản lý có toàn bộ quyền quản trị và kế thừa chức năng nhân viên.</p>
        @endif
    </div>
</aside>
</div>
@endsection
