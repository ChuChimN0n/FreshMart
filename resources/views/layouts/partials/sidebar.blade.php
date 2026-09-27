@php
    $links = ($managementLinks ?? collect())->filter(fn ($item) => is_array($item) && isset($item['route']));
    $overviewLinks = $links->where('group', 'overview')->values();
    $salesLinks = $links->where('group', 'sales')->values();
    $adminLinks = $links->where('group', 'admin')->values();
    // Fallback: neu item cu chua co group thi roi vao nhom Kinh doanh / Quan tri theo route
    $ungrouped = $links->filter(fn ($item) => empty($item['group'] ?? null))->values();
    $user = Auth::user();
@endphp

<nav class="mgmt-nav flex-1 overflow-y-auto px-3 py-4 space-y-6">
    @if($overviewLinks->isNotEmpty())
        <div>
            <p class="sidebar-section">Tổng quan</p>
            <div class="space-y-1">
                @foreach($overviewLinks as $item)
                    <a href="{{ route($item['route']) }}"
                       class="sidebar-link {{ request()->routeIs($item['active'] ?? $item['route']) ? 'active' : '' }}">
                        <i class="bi {{ $item['icon'] ?? 'bi-circle' }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($salesLinks->isNotEmpty() || $ungrouped->isNotEmpty())
        <div>
            <p class="sidebar-section">Kinh doanh</p>
            <div class="space-y-1">
                @foreach($salesLinks as $item)
                    <a href="{{ route($item['route']) }}"
                       class="sidebar-link {{ request()->routeIs($item['active'] ?? $item['route']) ? 'active' : '' }}">
                        <i class="bi {{ $item['icon'] ?? 'bi-circle' }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
                @foreach($ungrouped as $item)
                    <a href="{{ route($item['route']) }}"
                       class="sidebar-link {{ request()->routeIs($item['active'] ?? $item['route']) ? 'active' : '' }}">
                        <i class="bi {{ $item['icon'] ?? 'bi-circle' }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if($adminLinks->isNotEmpty())
        <div>
            <p class="sidebar-section">Quản trị</p>
            <div class="space-y-1">
                @foreach($adminLinks as $item)
                    @if(!empty($item['children']) && is_array($item['children']))
                        @php
                            $visibleChildren = collect($item['children'])
                                ->filter(fn ($child) => $user && $user->canAccessRoute($child['route'] ?? ''))
                                ->values();
                            $isChildActive = request()->routeIs($item['active'] ?? $item['route']);
                        @endphp
                        @if($visibleChildren->isNotEmpty())
                            <div>
                                <button type="button"
                                        onclick="toggleSidebarSubmenu('submenu-{{ md5($item['route']) }}', this)"
                                        class="sidebar-link w-full {{ $isChildActive ? 'active' : '' }}"
                                        aria-expanded="{{ $isChildActive ? 'true' : 'false' }}">
                                    <i class="bi {{ $item['icon'] ?? 'bi-circle' }}"></i>
                                    <span class="flex-1 text-left">{{ $item['label'] }}</span>
                                    <i class="bi bi-chevron-down submenu-chevron text-[10px] opacity-70 transition-transform {{ $isChildActive ? 'rotate-180' : '' }}"></i>
                                </button>
                                <div id="submenu-{{ md5($item['route']) }}"
                                     class="submenu ml-9 mt-1 space-y-1 border-l border-white/10 pl-3 {{ $isChildActive ? '' : 'hidden' }}">
                                    @foreach($visibleChildren as $child)
                                        <a href="{{ route($child['route']) }}"
                                           class="sidebar-sublink {{ request()->routeIs($child['active'] ?? $child['route']) ? 'active' : '' }}">
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <a href="{{ route($item['route']) }}"
                           class="sidebar-link {{ request()->routeIs($item['active'] ?? $item['route']) ? 'active' : '' }}">
                            <i class="bi {{ $item['icon'] ?? 'bi-circle' }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</nav>
