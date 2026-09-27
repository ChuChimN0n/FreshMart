@props(['tuNgay' => null, 'denNgay' => null])
<div class="bg-white rounded-xl shadow-sm p-4 mb-5">
    <form method="GET" class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
            <input type="date" name="tuNgay" value="{{ $tuNgay ?? request('tuNgay') }}" class="bhx-input" required>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
            <input type="date" name="denNgay" value="{{ $denNgay ?? request('denNgay') }}" class="bhx-input" required>
        </div>
        <button type="submit" class="bhx-btn-primary"><i class="bi bi-bar-chart"></i> Thống kê</button>
    </form>
</div>

@if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">{{ $errors->first() }}</div>
@endif
