@foreach($managementLinks as $item)
    <a href="{{ route($item['route']) }}" class="{{ ($mobile ?? false) ? '' : 'nav-link-sm' }} {{ request()->routeIs($item['active']) ? 'active' : '' }}">
        <i class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
    </a>
@endforeach
