@extends('layouts.management')
@section('title', 'Quản lý vai trò')
@section('content')
<x-page-header title="Quản lý vai trò & quyền"
    :subtitle="'Tổng '.$vaiTros->count().' vai trò trong hệ thống'"
    :actionUrl="route('admin.vaitro.create')" actionLabel="Thêm vai trò" />

<div class="space-y-4">
    @foreach($vaiTros as $vt)
        @php $isSystem = in_array($vt->maVT, [\App\Models\VaiTro::ADMIN_ID, \App\Models\VaiTro::STAFF_ID, \App\Models\VaiTro::KHACH_HANG_ID]); @endphp
        <div class="bg-white rounded-xl shadow-sm p-5">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div class="flex items-center gap-3">
                    <span class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shrink-0">
                        <i class="bi bi-shield-lock"></i>
                    </span>
                    <div>
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            {{ $vt->tenVT }}
                            @if($isSystem)
                                <span class="bhx-tag bg-gray-100 text-gray-500">Hệ thống</span>
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500">{{ $vt->moTa ?: 'Chưa có mô tả' }} · {{ $vt->quyens->count() }} quyền</p>
                    </div>
                </div>
                <div class="flex gap-1.5 items-center">
                    <a href="{{ route('admin.vaitro.edit', $vt) }}" title="Sửa vai trò"
                       class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition">
                        <i class="bi bi-pencil"></i>
                    </a>
                    @if(! $isSystem)
                        <form method="POST" action="{{ route('admin.vaitro.destroy', $vt) }}" class="inline" data-confirm="Xóa vai trò {{ $vt->tenVT }}?">
                            @csrf @method('DELETE')
                            <button type="submit" title="Xóa vai trò"
                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="mt-3 flex flex-wrap gap-1.5">
                @forelse($vt->quyens as $q)
                    <span class="bhx-tag bg-bhx-50 text-bhx-700">{{ $q->tenQuyen }}</span>
                @empty
                    <span class="text-gray-400 text-xs">Chưa có quyền nào</span>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
@endsection
