@props(['title', 'subtitle' => null, 'actionUrl' => null, 'actionLabel' => null, 'actionIcon' => 'bi-plus-lg'])
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">{{ $title }}</h1>
        @if($subtitle)
            <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
        @endif
    </div>
    @if($actionUrl)
        <a href="{{ $actionUrl }}" class="bhx-btn-primary">
            <i class="bi {{ $actionIcon }}"></i> {{ $actionLabel }}
        </a>
    @endif
</div>
