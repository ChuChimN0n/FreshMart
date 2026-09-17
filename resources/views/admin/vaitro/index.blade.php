@extends('layouts.app')
@section('title', 'Quản lý vai trò')
@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Quản lý vai trò & quyền</h1>
    <a href="{{ route('admin.vaitro.create') }}" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">+ Thêm vai trò</a>
</div>

<div class="space-y-4">
    @foreach($vaiTros as $vt)
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-start">
            <div>
                <h3 class="font-bold text-lg">{{ $vt->tenVT }}</h3>
                <p class="text-sm text-gray-500">{{ $vt->moTa }}</p>
            </div>
            <div class="flex gap-3 items-center">
                <a href="{{ route('admin.vaitro.edit', $vt) }}" class="text-blue-600 hover:underline text-sm">Sửa</a>
                @if(! in_array($vt->maVT, [\App\Models\VaiTro::ADMIN_ID, \App\Models\VaiTro::STAFF_ID, \App\Models\VaiTro::KHACH_HANG_ID]))
                <form method="POST" action="{{ route('admin.vaitro.destroy', $vt) }}" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-600 hover:underline text-sm" onclick="return confirm('Xóa vai trò này?')">Xóa</button>
                </form>
                @endif
            </div>
        </div>
        <div class="mt-3 flex flex-wrap gap-2">
            @forelse($vt->quyens as $q)
            <span class="bg-bhx-100 text-bhx-700 px-2 py-1 rounded text-xs">{{ $q->tenQuyen }}</span>
            @empty
            <span class="text-gray-400 text-xs">Chưa có quyền</span>
            @endforelse
        </div>
    </div>
    @endforeach
</div>
@endsection
