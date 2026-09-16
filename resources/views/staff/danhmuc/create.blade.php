@extends('layouts.app')
@section('title', 'Thêm danh mục')
@section('content')
<h1 class="text-2xl font-bold mb-4">Thêm danh mục mới</h1>
<div class="bg-white rounded-lg shadow p-6 max-w-lg">
    <form method="POST" action="{{ route('staff.danhmuc.store') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Tên danh mục *</label>
            <input type="text" name="tenDM" value="{{ old('tenDM') }}" class="w-full border rounded px-3 py-2 @error('tenDM') border-red-500 @enderror" required>
            @error('tenDM') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-bhx-500 text-white px-4 py-2 rounded hover:bg-bhx-600">Thêm</button>
            <a href="{{ route('staff.danhmuc.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Hủy</a>
        </div>
    </form>
</div>
@endsection
