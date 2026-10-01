@extends('layouts.management')
@section('title', $taiKhoan->isCustomer() ? 'Thông tin tài khoản' : 'Sửa tài khoản')
@section('content')
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('admin.taikhoan.index') }}" class="hover:text-bhx-600 transition">Tài khoản</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">{{ $taiKhoan->hoTen }}</span>
</nav>

<div class="mb-5 flex items-center gap-4">
    <span class="w-14 h-14 rounded-full bg-bhx-100 text-bhx-700 flex items-center justify-center text-2xl font-bold shrink-0">
        {{ mb_strtoupper(mb_substr($taiKhoan->hoTen, 0, 1)) }}
    </span>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $taiKhoan->isCustomer() ? 'Thông tin tài khoản' : 'Sửa tài khoản' }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $taiKhoan->vaiTro->tenVT ?? '' }} · {{ $taiKhoan->trangThai == 'HOAT_DONG' ? 'Hoạt động' : 'Bị khóa' }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
<div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5">
    @if($taiKhoan->isCustomer())
        {{-- KHÁCH HÀNG: Chỉ xem thông tin --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach([['Họ tên', $taiKhoan->hoTen], ['Tên đăng nhập', $taiKhoan->tenDangNhap], ['Email', $taiKhoan->email], ['Số điện thoại', $taiKhoan->soDienThoai], ['Địa chỉ', $taiKhoan->diaChi ?: '—'], ['Vai trò', $taiKhoan->vaiTro->tenVT ?? ''], ['Trạng thái', $taiKhoan->trangThai == 'HOAT_DONG' ? 'Hoạt động' : 'Bị khóa']] as [$lb, $val])
                <div>
                    <label class="block text-xs font-medium uppercase tracking-wide text-gray-400 mb-1">{{ $lb }}</label>
                    <p class="bhx-input bg-gray-50">{{ $val }}</p>
                </div>
            @endforeach
        </div>
        <div class="flex gap-2 mt-4">
            <a href="{{ route('admin.taikhoan.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Quay lại</a>
        </div>
    @else
        {{-- NHÂN VIÊN / ADMIN: Sửa tất cả + đổi MK --}}
        <form method="POST" action="{{ route('admin.taikhoan.update', $taiKhoan) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên <span class="text-red-500">*</span></label>
                    <input type="text" name="hoTen" value="{{ old('hoTen', $taiKhoan->hoTen) }}" class="bhx-input" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên đăng nhập</label>
                    <input type="text" value="{{ $taiKhoan->tenDangNhap }}" class="bhx-input bg-gray-50" disabled>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $taiKhoan->email) }}" class="bhx-input @error('email') !border-red-500 @enderror" required>
                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại <span class="text-red-500">*</span></label>
                    <input type="text" name="soDienThoai" value="{{ old('soDienThoai', $taiKhoan->soDienThoai) }}" class="bhx-input @error('soDienThoai') !border-red-500 @enderror" required>
                    @error('soDienThoai') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Địa chỉ</label>
                    <input type="text" name="diaChi" value="{{ old('diaChi', $taiKhoan->diaChi) }}" class="bhx-input">
                </div>
                <div>
                    <x-searchable-select name="maVT" label="Vai trò" :required="true"
                        placeholder="-- Chọn vai trò --" searchPlaceholder="Gõ để tìm vai trò..."
                        :options="$vaiTros->map(fn ($vt) => ['value' => $vt->maVT, 'label' => $vt->tenVT])->all()"
                        :selected="old('maVT', $taiKhoan->maVT)" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới (để trống nếu không đổi)</label>
                    <div class="pw-wrap">
                        <input type="password" name="matKhau" class="bhx-input pr-10">
                        <span class="pw-toggle" onclick="togglePw(this)">👁</span>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái <span class="text-red-500">*</span></label>
                    <select name="trangThai" class="bhx-input" required>
                        <option value="HOAT_DONG" {{ old('trangThai', $taiKhoan->trangThai) == 'HOAT_DONG' ? 'selected' : '' }}>Hoạt động</option>
                        <option value="KHOA" {{ old('trangThai', $taiKhoan->trangThai) == 'KHOA' ? 'selected' : '' }}>Khóa</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Cập nhật</button>
                <a href="{{ route('admin.taikhoan.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Hủy</a>
            </div>
        </form>
    @endif
</div>
<aside class="bg-white rounded-xl shadow-sm p-5 lg:sticky lg:top-5">
    <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
        <span class="w-7 h-7 rounded-lg bg-bhx-50 text-bhx-600 flex items-center justify-center"><i class="bi bi-person-badge"></i></span>
        Tóm tắt tài khoản
    </h2>
    @if($taiKhoan->isCustomer())
        <div class="flex items-center gap-3 mb-4">
            <span class="w-12 h-12 rounded-full bg-bhx-100 text-bhx-700 flex items-center justify-center text-xl font-bold shrink-0">
                {{ mb_strtoupper(mb_substr($taiKhoan->hoTen, 0, 1)) }}
            </span>
            <div class="min-w-0">
                <p class="font-bold text-gray-800 truncate">{{ $taiKhoan->hoTen }}</p>
                <p class="text-sm text-gray-500">{{ $taiKhoan->vaiTro->tenVT ?? '' }}</p>
            </div>
        </div>
        <dl class="text-sm space-y-2">
            <div class="flex justify-between gap-2"><dt class="text-gray-500">Tên đăng nhập</dt><dd class="font-medium text-right">{{ $taiKhoan->tenDangNhap }}</dd></div>
            <div class="flex justify-between gap-2"><dt class="text-gray-500">Email</dt><dd class="font-medium text-right break-all">{{ $taiKhoan->email }}</dd></div>
            <div class="flex justify-between gap-2"><dt class="text-gray-500">Trạng thái</dt><dd><span class="bhx-tag {{ $taiKhoan->trangThai == 'HOAT_DONG' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $taiKhoan->trangThai == 'HOAT_DONG' ? 'Hoạt động' : 'Bị khóa' }}</span></dd></div>
        </dl>
    @else
        <div class="flex items-center gap-3 mb-4">
            <span id="ttAvatar" class="w-12 h-12 rounded-full bg-bhx-100 text-bhx-700 flex items-center justify-center text-xl font-bold shrink-0">?</span>
            <div class="min-w-0">
                <p id="ttHoTen" class="font-bold text-gray-800 truncate">—</p>
                <p id="ttVaiTro" class="text-sm text-gray-500">—</p>
            </div>
        </div>
        <dl class="text-sm space-y-2">
            <div class="flex justify-between gap-2"><dt class="text-gray-500">Tên đăng nhập</dt><dd class="font-medium text-right">{{ $taiKhoan->tenDangNhap }}</dd></div>
            <div class="flex justify-between gap-2"><dt class="text-gray-500">Email</dt><dd id="ttEmail" class="font-medium text-right break-all">—</dd></div>
            <div class="flex justify-between gap-2"><dt class="text-gray-500">Số điện thoại</dt><dd id="ttSdt" class="font-medium text-right">—</dd></div>
            <div class="flex justify-between gap-2"><dt class="text-gray-500">Trạng thái</dt><dd id="ttTrangThai"><span class="bhx-tag bg-green-100 text-green-700">Hoạt động</span></dd></div>
        </dl>
        <script>
            function updateTomTatTK() {
                var get = function (name) {
                    var el = document.querySelector('input[name=' + name + ']');
                    return (el && el.value.trim()) || '—';
                };
                var hoTen = get('hoTen');
                document.getElementById('ttHoTen').textContent = hoTen;
                document.getElementById('ttAvatar').textContent = hoTen === '—' ? '?' : hoTen.charAt(0).toUpperCase();
                document.getElementById('ttEmail').textContent = get('email');
                document.getElementById('ttSdt').textContent = get('soDienThoai');
                var roleText = document.querySelector('[data-searchable-select] [data-ss-input]');
                document.getElementById('ttVaiTro').textContent = (roleText && roleText.value.trim()) || '—';
                var tt = document.querySelector('select[name=trangThai]');
                var badge = document.getElementById('ttTrangThai');
                if (tt && tt.value === 'KHOA') {
                    badge.innerHTML = '<span class="bhx-tag bg-red-100 text-red-700">Bị khóa</span>';
                } else {
                    badge.innerHTML = '<span class="bhx-tag bg-green-100 text-green-700">Hoạt động</span>';
                }
            }
            document.querySelectorAll('input[name=hoTen], input[name=email], input[name=soDienThoai]').forEach(function (el) {
                el.addEventListener('input', updateTomTatTK);
            });
            var roleHidden = document.querySelector('input[name=maVT][data-ss-value]');
            if (roleHidden) roleHidden.addEventListener('change', updateTomTatTK);
            var statusSel = document.querySelector('select[name=trangThai]');
            if (statusSel) statusSel.addEventListener('change', updateTomTatTK);
            updateTomTatTK();
        </script>
    @endif
</aside>
</div>
@endsection
