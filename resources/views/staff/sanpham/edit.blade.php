@extends('layouts.management')
@section('title', 'Sửa sản phẩm')
@section('content')
{{-- ===== Breadcrumb ===== --}}
<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('staff.sanpham.index') }}" class="hover:text-bhx-600 transition">Quản lý sản phẩm</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium truncate max-w-[300px]">{{ $sanPham->tenSP }}</span>
</nav>

<div class="mb-5">
    <h1 class="text-2xl font-bold text-gray-800">Sửa sản phẩm</h1>
    <p class="text-sm text-gray-500 mt-1">Mã #{{ $sanPham->maSP }} · Cập nhật thông tin hiển thị trên cửa hàng</p>
</div>

<form method="POST" action="{{ route('staff.sanpham.update', $sanPham) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
        {{-- ===== Cột trái: thông tin chung + mô tả ===== --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-bhx-50 text-bhx-600 flex items-center justify-center text-sm"><i class="bi bi-info-circle"></i></span>
                    Thông tin chung
                </h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên sản phẩm <span class="text-red-500">*</span></label>
                    <input type="text" name="tenSP" value="{{ old('tenSP', $sanPham->tenSP) }}" class="bhx-input @error('tenSP') !border-red-500 @enderror" required>
                    @error('tenSP') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mã sản phẩm (SKU) <i class="bi bi-lock-fill text-xs text-gray-400" title="Mã do hệ thống quản lý"></i></label>
                    <input type="text" value="{{ $sanPham->sku ?: '—' }}" readonly
                           class="bhx-input font-mono bg-gray-50 text-gray-600">
                    <p class="text-xs text-gray-400 mt-1">Mã nội bộ #{{ $sanPham->maSP }} và SKU không thay đổi.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-searchable-select name="maDM" label="Danh mục" :required="true"
                            placeholder="-- Chọn danh mục --" searchPlaceholder="Gõ để tìm danh mục..."
                            :options="$danhMucs->map(fn ($dm) => ['value' => $dm->maDM, 'label' => $dm->tenDM])->all()"
                            :selected="old('maDM', $sanPham->maDM)" />
                    </div>
                    <div>
                        <x-searchable-select name="maNCC" label="Nhà cung cấp" :required="true"
                            placeholder="-- Chọn nhà cung cấp --" searchPlaceholder="Gõ để tìm nhà cung cấp..."
                            :options="$nhaCungCaps->map(fn ($ncc) => ['value' => $ncc->maNCC, 'label' => $ncc->tenNCC])->all()"
                            :selected="old('maNCC', $sanPham->maNCC)" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giá bán (đ) <span class="text-red-500">*</span></label>
                        <input type="number" name="giaBan" value="{{ old('giaBan', $sanPham->giaBan) }}" min="0" step="500" class="bhx-input" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tồn kho</label>
                        <input type="number" value="{{ $sanPham->soLuong }}" min="0" class="bhx-input bg-gray-50" disabled>
                        <p class="text-xs text-gray-400 mt-1">Đổi qua nhập / bán / hoàn kho, không sửa tay.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mức tồn tối thiểu <span class="text-red-500">*</span></label>
                        <input type="number" name="mucTonToiThieu" value="{{ old('mucTonToiThieu', $sanPham->mucTonToiThieu) }}" min="0" class="bhx-input" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Đơn vị tính <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['kg','quả','bó','gói','chai','hộp','thùng','bịch','cây','củ','cái'] as $dv)
                                <label class="cursor-pointer">
                                    <input type="radio" name="donVi" value="{{ $dv }}" {{ old('donVi', $sanPham->donVi ?? 'kg') == $dv ? 'checked' : '' }} class="peer sr-only" required>
                                    <span class="inline-block px-3.5 py-1.5 rounded-lg border border-gray-300 text-sm text-gray-600 peer-checked:bg-bhx-500 peer-checked:border-bhx-500 peer-checked:text-white hover:border-bhx-400 transition">{{ $dv }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-bhx-50 text-bhx-600 flex items-center justify-center text-sm"><i class="bi bi-card-text"></i></span>
                    Mô tả sản phẩm
                </h2>
                <textarea name="moTa" rows="4" class="bhx-input">{{ old('moTa', $sanPham->moTa) }}</textarea>
            </div>
        </div>

        {{-- ===== Cột phải: hình ảnh + trạng thái ===== --}}
        <div class="space-y-5">
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-bhx-50 text-bhx-600 flex items-center justify-center text-sm"><i class="bi bi-image"></i></span>
                    Hình ảnh
                </h2>
                <div class="aspect-square rounded-xl bg-gray-50 border-2 border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400 overflow-hidden mb-3">
                    <img id="previewImg" src="{{ $sanPham->hinhAnh ? asset('storage/'.$sanPham->hinhAnh) : '' }}" class="{{ $sanPham->hinhAnh ? '' : 'hidden' }} w-full h-full object-cover" alt="Xem trước">
                    <div id="previewPlaceholder" class="{{ $sanPham->hinhAnh ? 'hidden' : 'flex' }} flex-col items-center">
                        <i class="bi bi-cloud-upload text-4xl mb-2"></i>
                        <p class="text-xs">Chưa có ảnh</p>
                    </div>
                </div>
                <input type="file" name="hinhAnh" id="hinhAnh" accept="image/*" class="bhx-input">
                <p class="text-xs text-gray-400 mt-2">Để trống nếu không đổi ảnh. JPG, PNG tối đa 2MB.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-bhx-50 text-bhx-600 flex items-center justify-center text-sm"><i class="bi bi-eye"></i></span>
                    Trạng thái
                </h2>
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:border-bhx-400 has-checked:border-bhx-500 has-checked:bg-bhx-50/50 transition">
                        <input type="radio" name="trangThai" value="{{ \App\Models\SanPham::DANG_BAN }}" {{ old('trangThai', $sanPham->trangThai) == \App\Models\SanPham::DANG_BAN ? 'checked' : '' }} class="accent-green-600" required>
                        <span>
                            <span class="block text-sm font-medium text-gray-800">Đang bán</span>
                            <span class="block text-xs text-gray-400">Hiển thị trên cửa hàng</span>
                        </span>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 cursor-pointer hover:border-gray-400 has-checked:border-gray-500 has-checked:bg-gray-50 transition">
                        <input type="radio" name="trangThai" value="{{ \App\Models\SanPham::NGUNG_BAN }}" {{ old('trangThai', $sanPham->trangThai) == \App\Models\SanPham::NGUNG_BAN ? 'checked' : '' }} class="accent-gray-600">
                        <span>
                            <span class="block text-sm font-medium text-gray-800">Ngừng bán</span>
                            <span class="block text-xs text-gray-400">Ẩn khỏi cửa hàng</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Thanh hành động ===== --}}
    <div class="sticky bottom-4 z-10 mt-5 bg-white rounded-xl shadow-md p-4 flex items-center justify-end gap-2">
        <a href="{{ route('staff.sanpham.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">Hủy</a>
        <button type="submit" class="bhx-btn-primary"><i class="bi bi-check-lg"></i> Cập nhật</button>
    </div>
</form>

<script>
    document.getElementById('hinhAnh').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const img = document.getElementById('previewImg');
        const placeholder = document.getElementById('previewPlaceholder');
        if (file) {
            const reader = new FileReader();
            reader.onload = function (ev) {
                img.src = ev.target.result;
                img.classList.remove('hidden');
                placeholder.classList.add('hidden');
                placeholder.classList.remove('flex');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
