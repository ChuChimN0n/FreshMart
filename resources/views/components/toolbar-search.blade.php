@props(['name' => 'search', 'placeholder' => 'Tìm kiếm...', 'value' => null])
@php $value = $value ?? request($name); @endphp
<div class="relative flex-1 min-w-0">
    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
    <input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}" class="bhx-input pl-9 pr-9">
    @if($value)
        <a href="#" onclick="event.preventDefault(); { const u = new URL(window.location.href); u.searchParams.delete('{{ $name }}'); u.searchParams.delete('page'); window.location.href = u.toString(); }"
           class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" title="Xóa tìm kiếm">
            <i class="bi bi-x-lg"></i>
        </a>
    @endif
</div>
