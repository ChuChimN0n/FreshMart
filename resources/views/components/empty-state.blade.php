@props(['icon' => 'bi-inbox', 'title' => 'Không có dữ liệu', 'desc' => null, 'actionUrl' => null, 'actionLabel' => null])
<div class="px-4 py-12 text-center">
    <div class="text-bhx-200 text-5xl mb-3"><i class="bi {{ $icon }}"></i></div>
    <p class="text-gray-500 font-medium">{{ $title }}</p>
    @if($desc)
        <p class="text-sm text-gray-400 mt-1 mb-4">{{ $desc }}</p>
    @endif
    @if($actionUrl)
        <a href="{{ $actionUrl }}" class="bhx-btn-primary text-sm">{{ $actionLabel }}</a>
    @endif
</div>
