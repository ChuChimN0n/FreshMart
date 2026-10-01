@extends('layouts.management')
@section('title', 'Tạo phiếu nhập')
@section('content')
@php
    $oldItems = old('items', []);
@endphp

<nav class="text-sm text-gray-500 mb-4 flex items-center gap-2">
    <a href="{{ route('staff.nhaphang.index') }}" class="hover:text-bhx-600 transition">Nhập hàng</a>
    <i class="bi bi-chevron-right text-xs"></i>
    <span class="text-gray-800 font-medium">Tạo phiếu nhập</span>
</nav>

<h1 class="text-2xl font-bold text-gray-800 mb-1">Tạo phiếu nhập</h1>
<p class="text-sm text-gray-500 mb-5">Chọn nhà cung cấp trước — danh sách sản phẩm tự lọc theo NCC đó. Lưu phiếu ở trạng thái chờ xác nhận, tồn kho chỉ tăng khi xác nhận.</p>

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('staff.nhaphang.store') }}" id="phieuForm">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <h2 class="font-bold text-gray-800 mb-4">Thông tin chung</h2>
            <label class="block text-sm font-medium text-gray-600 mb-1">Nhà cung cấp <span class="text-red-500">*</span></label>
            <x-searchable-select name="maNCC" :required="true"
                placeholder="— Chọn NCC —" searchPlaceholder="Gõ để tìm NCC..."
                :options="$nhaCungCaps->map(fn ($ncc) => ['value' => $ncc->maNCC, 'label' => $ncc->tenNCC.' ('.$ncc->codeNCC.')'])->values()->all()"
                :selected="old('maNCC', $maNCC)" />
            <label class="block text-sm font-medium text-gray-600 mt-4 mb-1" for="ghiChu">Ghi chú</label>
            <textarea name="ghiChu" id="ghiChu" rows="3" class="bhx-input" placeholder="Ghi chú thêm...">{{ old('ghiChu') }}</textarea>
            <div class="mt-5 rounded-xl bg-gray-50 p-4 text-sm">
                <div class="flex justify-between text-gray-500"><span>Tổng số lượng</span><span id="tongSL" class="font-semibold text-gray-800">0</span></div>
                <div class="flex justify-between mt-1"><span class="font-semibold text-gray-700">Tổng tiền</span><span id="tongTien" class="font-bold text-bhx-700 text-lg">0đ</span></div>
            </div>
            <button type="submit" class="bhx-btn-primary w-full mt-4 justify-center">
                <i class="bi bi-save"></i> Lưu phiếu nhập
            </button>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-800">Sản phẩm nhập</h2>
                <div class="flex gap-2">
                    <button type="button" id="themTatCa" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        <i class="bi bi-plus-square"></i> Thêm tất cả
                    </button>
                    <button type="button" id="themDong" class="inline-flex items-center gap-1.5 rounded-lg border border-bhx-500 text-bhx-600 px-3 py-2 text-sm font-medium hover:bg-bhx-50 transition">
                        <i class="bi bi-plus-lg"></i> Thêm dòng
                    </button>
                </div>
            </div>
            <p id="chuaChonNCC" class="text-sm text-amber-600 bg-amber-50 rounded-lg px-4 py-3 {{ old('maNCC', $maNCC) ? 'hidden' : '' }}">Vui lòng chọn nhà cung cấp để thấy sản phẩm nhập được.</p>
            <p id="nccKhongCoSP" class="hidden text-sm text-gray-500 bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">Nhà cung cấp này chưa có sản phẩm nào đang bán.</p>
            <div id="nccChipsWrap" class="mb-4">
                <p class="text-xs font-medium text-gray-500 mb-2">Món nhà cung cấp này bán — bấm để thêm vào phiếu:</p>
                <div id="nccChips" class="flex flex-wrap gap-2"></div>
            </div>
            <p id="nccToast" class="hidden text-sm text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-4 py-2 mb-3"></p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="bangNhap">
                    <thead>
                        <tr class="text-left text-xs uppercase text-gray-500 border-b">
                            <th class="py-2 pr-2">Sản phẩm</th>
                            <th class="py-2 pr-2 w-24">Số lượng</th>
                            <th class="py-2 pr-2 w-32">Giá nhập</th>
                            <th class="py-2 w-32 text-right">Thành tiền</th>
                            <th class="py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="dongNhap"></tbody>
                </table>
            </div>
        </div>
    </div>
</form>

<script>
const SP_BY_ID = @json($sanPhamById);
const NCC_SP_MAP = @json($nccSanPhamMap);
const OLD_ITEMS = @json(array_values($oldItems));
const PRESET_MASP = @json($presetMaSP ?? 0);
const nccHidden = document.querySelector('input[name="maNCC"][data-ss-value]');
const nccText = document.querySelector('[data-searchable-select] [data-ss-input]');
const getMaNCC = () => nccHidden.value;
let dongIndex = 0;

function fmt(n) { return Number(n || 0).toLocaleString('vi-VN'); }
function spIdsForNCC(maNCC) { return (NCC_SP_MAP[maNCC] || []).filter(id => SP_BY_ID[id]); }

function selectedIds(exceptSelect) {
    const ids = [];
    document.querySelectorAll('#dongNhap select.sp-chon').forEach(s => {
        if (s !== exceptSelect && s.value) ids.push(s.value);
    });
    return ids;
}

function tinhTong() {
    let tsl = 0, tt = 0;
    document.querySelectorAll('#dongNhap tr').forEach(tr => {
        const sl = parseInt(tr.querySelector('.sl').value) || 0;
        const gia = parseFloat(tr.querySelector('.gia').value) || 0;
        tsl += sl; tt += sl * gia;
        tr.querySelector('.tt').textContent = fmt(sl * gia) + 'đ';
    });
    document.getElementById('tongSL').textContent = tsl;
    document.getElementById('tongTien').textContent = fmt(tt) + 'đ';
}

function buildOptions(maNCC, selectedId) {
    return spIdsForNCC(maNCC).map(id => {
        const sp = SP_BY_ID[id];
        const sel = String(id) === String(selectedId) ? ' selected' : '';
        return `<option value="${sp.maSP}"${sel}>${sp.tenSP} (tồn: ${sp.ton} ${sp.donVi})</option>`;
    }).join('');
}

function renderChips() {
    const wrap = document.getElementById('nccChips');
    const maNCC = getMaNCC();
    const ids = spIdsForNCC(maNCC);
    document.getElementById('nccKhongCoSP').classList.toggle('hidden', !maNCC || ids.length > 0);
    wrap.innerHTML = ids.map(id => {
        const sp = SP_BY_ID[id];
        return `<button type="button" data-chip="${sp.maSP}" class="inline-flex items-center gap-1.5 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm text-gray-700 shadow-sm transition hover:border-bhx-400 hover:text-bhx-700 disabled:opacity-40 disabled:pointer-events-none"><i class="bi bi-plus-circle"></i>${sp.tenSP} <span class="text-xs text-gray-400">(tồn ${sp.ton})</span></button>`;
    }).join('');
    wrap.querySelectorAll('[data-chip]').forEach(chip => {
        chip.addEventListener('click', () => themDong({ maSP: chip.dataset.chip, soLuong: 1, giaNhap: '' }));
    });
    syncChips();
}

function syncChips() {
    const taken = selectedIds(null).map(String);
    document.querySelectorAll('#nccChips [data-chip]').forEach(chip => {
        chip.disabled = taken.includes(String(chip.dataset.chip));
    });
}

let toastTimer = null;
function showToast(msg) {
    const toast = document.getElementById('nccToast');
    toast.textContent = msg;
    toast.classList.remove('hidden');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.add('hidden'), 4000);
}

function themDong(preset) {
    const maNCC = getMaNCC();
    if (!maNCC) {
        document.getElementById('chuaChonNCC').classList.remove('hidden');
        nccText.focus();
        return;
    }
    const ids = spIdsForNCC(maNCC);
    if (!ids.length) {
        renderChips();
        showToast('Nhà cung cấp này chưa có sản phẩm nào đang bán.');
        return;
    }
    const taken = selectedIds(null).map(String);
    let first = ids.map(String).find(id => !taken.includes(id));
    if (preset && preset.maSP) {
        if (taken.includes(String(preset.maSP))) {
            alert('Sản phẩm này đã có trong phiếu. Hãy tăng số lượng ở dòng có sẵn.');
            return;
        }
        if (ids.map(String).includes(String(preset.maSP))) first = preset.maSP;
    }
    if (!first) {
        alert('Tất cả sản phẩm của NCC này đã có trong phiếu. Hãy tăng số lượng ở dòng có sẵn.');
        return;
    }
    const i = dongIndex++;
    const tr = document.createElement('tr');
    tr.className = 'border-b last:border-0';
    tr.innerHTML = `
        <td class="py-2 pr-2"><select name="items[${i}][maSP]" class="bhx-input sp-chon" required>${buildOptions(maNCC, first)}</select></td>
        <td class="py-2 pr-2"><input type="number" name="items[${i}][soLuong]" value="${preset?.soLuong ?? 1}" min="1" class="bhx-input sl" required oninput="tinhTong()"></td>
        <td class="py-2 pr-2"><input type="number" name="items[${i}][giaNhap]" value="${preset?.giaNhap ?? ''}" min="0" step="500" placeholder="Nhập giá" class="bhx-input gia" required oninput="tinhTong()"></td>
        <td class="py-2 text-right font-semibold tt">0đ</td>
        <td class="py-2 text-right"><button type="button" class="text-red-500 hover:text-red-700" onclick="this.closest('tr').remove();tinhTong();syncChips()"><i class="bi bi-trash"></i></button></td>`;
    document.getElementById('dongNhap').appendChild(tr);
    tr.querySelector('select.sp-chon').addEventListener('change', function () {
        if (selectedIds(this).includes(this.value)) {
            alert('Sản phẩm này đã có trong phiếu. Hãy tăng số lượng ở dòng có sẵn.');
            this.value = this.querySelector('option').value;
        }
        syncChips();
    });
    tinhTong();
    syncChips();
}

function themTatCa() {
    const maNCC = getMaNCC();
    if (!maNCC) {
        document.getElementById('chuaChonNCC').classList.remove('hidden');
        nccText.focus();
        return;
    }
    spIdsForNCC(maNCC).forEach(id => themDong({ maSP: id, soLuong: 1, giaNhap: '' }));
}

document.getElementById('themDong').addEventListener('click', () => themDong());
document.getElementById('themTatCa').addEventListener('click', themTatCa);
nccHidden.addEventListener('change', function () {
    const maNCC = this.value;
    document.getElementById('chuaChonNCC').classList.toggle('hidden', !!maNCC);
    const valid = new Set(spIdsForNCC(maNCC).map(String));
    let removed = 0;
    document.querySelectorAll('#dongNhap tr').forEach(tr => {
        const sel = tr.querySelector('select.sp-chon');
        if (!sel || !valid.has(String(sel.value))) { tr.remove(); removed++; }
    });
    if (removed > 0) showToast(`Đã bỏ ${removed} dòng không thuộc nhà cung cấp mới.`);
    renderChips();
    tinhTong();
});

// Dựng lại các dòng đã nhập khi validate lỗi (ưu tiên), nếu không thì dùng món preset từ kho/sản phẩm.
renderChips();
if (OLD_ITEMS.length) {
    OLD_ITEMS.forEach(item => themDong(item));
} else if (PRESET_MASP) {
    themDong({ maSP: PRESET_MASP, soLuong: 1, giaNhap: '' });
} else if (getMaNCC()) {
    themDong();
}
tinhTong();
syncChips();
</script>
@endsection
