@props(['url' => null, 'number' => 0, 'label' => '', 'icon' => 'bi-grid', 'color' => 'bhx', 'active' => false])
@php
    $tag = $url ? 'a' : 'div';
    $palettes = [
        'bhx' => ['bg' => 'bg-bhx-50', 'text' => 'text-bhx-600', 'ring' => 'ring-bhx-500'],
        'red' => ['bg' => 'bg-red-50', 'text' => 'text-red-600', 'ring' => 'ring-red-500'],
        'blue' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'ring' => 'ring-blue-500'],
        'green' => ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'ring' => 'ring-green-500'],
        'amber' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'ring' => 'ring-amber-500'],
        'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'ring' => 'ring-purple-500'],
    ];
    $p = $palettes[$color] ?? $palettes['bhx'];
    $ring = $active ? 'ring-2 '.$p['ring'] : '';
@endphp
<{{ $tag }} @if($url) href="{{ $url }}" @endif
    class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 transition {{ $url ? 'hover:shadow-md' : '' }} {{ $ring }}">
    <span class="w-11 h-11 rounded-full {{ $p['bg'] }} {{ $p['text'] }} flex items-center justify-center text-xl shrink-0">
        <i class="bi {{ $icon }}"></i>
    </span>
    <span>
        <span class="block text-2xl font-bold text-gray-800 leading-none">{{ $number }}</span>
        <span class="block text-xs text-gray-500 mt-1">{{ $label }}</span>
    </span>
</{{ $tag }}>
