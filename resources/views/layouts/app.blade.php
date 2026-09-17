<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Bách Hóa Xanh')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .pw-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none; color: #6b7280; }
        .pw-toggle:hover { color: #374151; }
        .pw-wrap { position: relative; }

        /* Nav links with icon + text */
        .nav-link-sm { display: flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; font-size: 0.8125rem; font-weight: 500; color: rgba(255,255,255,0.88); transition: all 0.18s ease; white-space: nowrap; }
        .nav-link-sm i { font-size: 0.95rem; }
        .nav-link-sm:hover { background: rgba(255,255,255,0.12); color: #fff; }
        .nav-link-sm.active { background: rgba(255,255,255,0.18); color: #fff; }

        /* Cart badge */
        .cart-badge { background: #e4002b; color: #fff; font-size: 0.6rem; font-weight: 700; min-width: 15px; height: 15px; border-radius: 9999px; display: inline-flex; align-items: center; justify-content: center; line-height: 1; padding: 0 3px; margin-left: 2px; }

        /* User dropdown */
        .user-dd { position: relative; }
        .user-dd-menu { position: absolute; right: 0; top: calc(100% + 6px); background: #fff; border-radius: 10px; box-shadow: 0 6px 24px rgba(0,0,0,0.12); min-width: 200px; opacity: 0; visibility: hidden; transform: translateY(-6px); transition: all 0.18s ease; z-index: 999; overflow: hidden; }
        .user-dd-menu.show { opacity: 1; visibility: visible; transform: translateY(0); }
        .user-dd-menu a, .user-dd-menu button { display: flex; align-items: center; gap: 8px; width: 100%; padding: 9px 14px; font-size: 0.8125rem; color: #374151; transition: background 0.12s ease; text-align: left; border: none; background: none; cursor: pointer; }
        .user-dd-menu a:hover, .user-dd-menu button:hover { background: #f3f4f6; }
        .user-dd-menu .dd-divider { height: 1px; background: #e5e7eb; margin: 4px 0; }

        /* Mobile menu */
        .mobile-nav { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
        .mobile-nav.open { max-height: calc(100dvh - 160px); overflow-y: auto; }
        .mobile-nav a, .mobile-nav button { display: flex; align-items: center; gap: 10px; padding: 11px 16px; font-size: 0.875rem; color: rgba(255,255,255,0.9); border-radius: 8px; transition: background 0.12s ease; }
        .mobile-nav a:hover, .mobile-nav button:hover { background: rgba(255,255,255,0.1); }
        .mobile-nav a.active { background: rgba(255,255,255,0.15); color: #fff; }
    </style>
</head>
<body class="bg-bhx-gray min-h-screen flex flex-col">
    <header class="sticky top-0 z-50">
        {{-- ===== Row 1: Logo + Search + User actions ===== --}}
        <div class="bg-bhx-700 text-white">
            <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center gap-4">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                    <span class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-bhx-600 shadow-sm">
                        <i class="bi bi-basket text-xl"></i>
                    </span>
                    <span class="leading-none">
                        <span class="block text-base md:text-lg font-extrabold tracking-tight">BÁCH HÓA XANH</span>
                        <span class="block text-[10px] text-bhx-100/90 mt-0.5 tracking-wide">Mua nhanh &bull; Mua dễ</span>
                    </span>
                </a>

                {{-- Search (desktop) --}}
                <form method="GET" action="{{ route('home') }}" class="hidden md:block flex-1 max-w-xl mx-auto">
                    <div class="relative w-full">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Bạn tìm gì ở Bách Hoá Xanh?"
                               class="w-full rounded-full bg-white px-5 py-2.5 pr-12 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-bhx-yellow">
                        <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 h-9 w-9 rounded-full bg-bhx-500 hover:bg-bhx-600 text-white flex items-center justify-center transition" aria-label="Tìm kiếm">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                {{-- Right: Auth --}}
                <div class="flex items-center gap-2 ml-auto shrink-0">
                    @auth
                        @if(Auth::user()->isCustomer())
                            <div class="hidden lg:block">
                                <a href="{{ route('giohang.index') }}" class="nav-link-sm">
                                    <i class="bi bi-cart3 text-lg"></i>
                                    @if($cartCount > 0)
                                        <span class="cart-badge">{{ $cartCount }}</span>
                                    @endif
                                </a>
                            </div>
                        @endif

                        {{-- Desktop user dropdown --}}
                        <div class="user-dd hidden lg:block" id="userDropdown">
                            <button onclick="toggleUserDropdown()" class="flex items-center gap-1.5 text-sm text-white/90 hover:text-white hover:bg-white/10 px-3 py-1.5 rounded-lg transition">
                                <i class="bi bi-person-circle text-base"></i>
                                <span class="max-w-[110px] truncate text-xs font-medium">{{ Auth::user()->hoTen }}</span>
                                <i class="bi bi-chevron-down text-[10px] opacity-60"></i>
                            </button>
                            <div class="user-dd-menu" id="userDropdownMenu">
                                <div class="px-3 py-2.5 border-b border-gray-100">
                                    <p class="font-medium text-gray-800 text-sm">{{ Auth::user()->hoTen }}</p>
                                    <p class="text-[11px] text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                @if($managementLinks->isNotEmpty())
                                    <a href="{{ route(Auth::user()->dashboardRoute()) }}"><i class="bi bi-speedometer2 text-gray-400"></i> Quản lý</a>
                                @endif
                                <a href="{{ route('profile') }}"><i class="bi bi-person text-gray-400"></i> Thông tin cá nhân</a>
                                @if(Auth::user()->isCustomer())
                                    <a href="{{ route('donhang.index') }}"><i class="bi bi-receipt text-gray-400"></i> Đơn hàng của tôi</a>
                                @endif
                                <a href="{{ route('change-password') }}"><i class="bi bi-key text-gray-400"></i> Đổi mật khẩu</a>
                                <div class="dd-divider"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"><i class="bi bi-box-arrow-right text-gray-400"></i> Đăng xuất</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs text-white/85 hover:text-white px-3 py-1.5 transition">
                            <i class="bi bi-person"></i> Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="bg-bhx-yellow text-bhx-900 px-4 py-1.5 rounded-lg text-xs font-bold hover:brightness-110 transition">Đăng ký</a>
                    @endauth

                    {{-- Mobile hamburger --}}
                    @auth
                    <button onclick="toggleMobileMenu()" class="lg:hidden text-white text-lg focus:outline-none p-2 rounded-lg hover:bg-white/10 transition" id="hamburger" aria-label="Mở menu" aria-controls="mobileMenu" aria-expanded="false">
                        <i class="bi bi-list" id="hamburgerIcon"></i>
                    </button>
                    @endauth
                </div>
            </div>

            {{-- Mobile search --}}
            <div class="md:hidden px-4 pb-2.5">
                <form method="GET" action="{{ route('home') }}" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Bạn tìm gì ở Bách Hoá Xanh?"
                           class="w-full rounded-full bg-white px-4 py-2 pr-10 text-sm text-gray-800 placeholder-gray-400 focus:outline-none">
                    <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 h-7 w-7 rounded-full bg-bhx-500 text-white flex items-center justify-center" aria-label="Tìm kiếm">
                        <i class="bi bi-search text-sm"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- ===== Row 2: Mobile cart + role nav ===== --}}
        <nav class="bg-bhx-800 text-white">
            <div class="max-w-7xl mx-auto px-4 flex items-center gap-1 overflow-x-auto">
                @auth
                    @if(Auth::user()->isCustomer())
                        <a href="{{ route('home') }}" class="nav-link-sm {{ request()->routeIs('home') ? 'active' : '' }}">
                            <i class="bi bi-house-door"></i> Trang chủ
                        </a>
                        <a href="{{ route('giohang.index') }}" class="nav-link-sm lg:hidden {{ request()->routeIs('giohang.*') ? 'active' : '' }}">
                            <i class="bi bi-cart3"></i> Giỏ hàng
                            @if(($cartCount ?? 0) > 0)
                                <span class="cart-badge">{{ $cartCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('donhang.index') }}" class="nav-link-sm {{ request()->routeIs('donhang.*') ? 'active' : '' }}">
                            <i class="bi bi-receipt"></i> Đơn hàng
                        </a>
                    @endif
                    @include('layouts.management-links')
                @else
                    <a href="{{ route('home') }}" class="nav-link-sm {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i> Trang chủ
                    </a>
                @endauth
            </div>
        </nav>

        {{-- ===== Mobile Menu ===== --}}
        @auth
        <div class="mobile-nav lg:hidden bg-bhx-800" id="mobileMenu">
            <div class="px-3 py-2">
                @if(Auth::user()->isCustomer())
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i> Trang chủ
                    </a>
                    <a href="{{ route('giohang.index') }}" class="{{ request()->routeIs('giohang.*') ? 'active' : '' }}">
                        <i class="bi bi-cart3"></i> Giỏ hàng
                        @if(($cartCount ?? 0) > 0)
                            <span class="ml-auto bg-bhx-red text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('donhang.index') }}" class="{{ request()->routeIs('donhang.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i> Đơn hàng
                    </a>
                    <div class="border-t border-white/10 my-1.5"></div>
                @endif
                @include('layouts.management-links', ['mobile' => true])

                <a href="{{ route('profile') }}"><i class="bi bi-person"></i> Thông tin cá nhân</a>
                <a href="{{ route('change-password') }}"><i class="bi bi-key"></i> Đổi mật khẩu</a>
                <div class="border-t border-white/10 my-1.5"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"><i class="bi bi-box-arrow-right"></i> Đăng xuất</button>
                </form>
            </div>
        </div>
        @endauth
    </header>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="max-w-7xl mx-auto mt-4 px-4 w-full" id="flash-success">
            <div class="bg-bhx-50 border border-bhx-400 text-bhx-800 px-4 py-3 rounded-lg flex justify-between items-center">
                <span class="flex items-center gap-2"><i class="bi bi-check-circle-fill text-bhx-500"></i> {{ session('success') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-bhx-800 hover:text-bhx-900 font-bold text-lg leading-none">&times;</button>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto mt-4 px-4 w-full" id="flash-error">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex justify-between items-center">
                <span class="flex items-center gap-2"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900 font-bold text-lg leading-none">&times;</button>
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto px-4 py-6 flex-1 w-full">
        @yield('content')
    </main>

    <footer class="bg-bhx-900 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-10 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-bhx-600 shadow">
                        <i class="bi bi-basket text-xl"></i>
                    </span>
                    <span class="leading-none">
                        <span class="block text-lg font-extrabold tracking-tight">BÁCH HÓA XANH</span>
                        <span class="block text-[10px] text-bhx-100/80 mt-0.5">Mua nhanh &bull; Mua dễ</span>
                    </span>
                </div>
                <p class="text-white/60 text-sm leading-relaxed">Hệ thống bán lẻ thực phẩm tươi ngon, an toàn với giá tốt mỗi ngày cho mọi gia đình Việt.</p>
                <div class="flex gap-2 mt-4">
                    <a href="#" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-sm uppercase tracking-wide mb-3 text-bhx-200">Về Bách Hóa Xanh</h4>
                <a href="{{ route('home') }}" class="block text-white/60 text-sm py-1 hover:text-white transition">Trang chủ</a>
                @auth
                    @if(Auth::user()->isCustomer())
                        <a href="{{ route('giohang.index') }}" class="block text-white/60 text-sm py-1 hover:text-white transition">Giỏ hàng</a>
                        <a href="{{ route('donhang.index') }}" class="block text-white/60 text-sm py-1 hover:text-white transition">Đơn hàng của tôi</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block text-white/60 text-sm py-1 hover:text-white transition">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="block text-white/60 text-sm py-1 hover:text-white transition">Đăng ký</a>
                @endauth
            </div>

            <div>
                <h4 class="font-semibold text-sm uppercase tracking-wide mb-3 text-bhx-200">Hỗ trợ khách hàng</h4>
                <a href="{{ route('profile') }}" class="block text-white/60 text-sm py-1 hover:text-white transition">Thông tin cá nhân</a>
                <a href="{{ route('change-password') }}" class="block text-white/60 text-sm py-1 hover:text-white transition">Đổi mật khẩu</a>
            </div>

            <div>
                <h4 class="font-semibold text-sm uppercase tracking-wide mb-3 text-bhx-200">Liên hệ</h4>
                <p class="text-white/60 text-sm py-1">📍 Yên Xá, Thanh Liệt, Hà Nội, Việt Nam</p>
                <p class="text-white/60 text-sm py-1">📞 1900 1234</p>
                <p class="text-white/60 text-sm py-1">✉️ hotro@bachhoaxanh.vn</p>
                <p class="text-white/60 text-sm py-1">🕗 08:00 - 21:00 hằng ngày</p>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="max-w-7xl mx-auto px-4 py-5 flex flex-col md:flex-row items-center justify-between gap-2 text-white/50 text-sm">
                <span>&copy; {{ date('Y') }} Bách Hóa Xanh. Tất cả quyền được bảo lưu.</span>
                <span>Thực phẩm tươi ngon &bull; Giá tốt mỗi ngày</span>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const icon = document.getElementById('hamburgerIcon');
            const button = document.getElementById('hamburger');
            if (!menu || !icon || !button) return;
            menu.classList.toggle('open');
            icon.className = menu.classList.contains('open') ? 'bi bi-x-lg' : 'bi bi-list';
            button.setAttribute('aria-expanded', String(menu.classList.contains('open')));
        }

        function toggleUserDropdown() {
            document.getElementById('userDropdownMenu').classList.toggle('show');
        }

        document.addEventListener('click', function(e) {
            const dd = document.getElementById('userDropdown');
            const menu = document.getElementById('userDropdownMenu');
            if (dd && !dd.contains(e.target)) {
                menu.classList.remove('show');
            }
        });

        function togglePw(btn) {
            const input = btn.parentElement.querySelector('input');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            btn.textContent = isPassword ? '🙈' : '👁';
        }

        setTimeout(() => { const el = document.getElementById('flash-success'); if (el) el.remove(); }, 4000);
        setTimeout(() => { const el = document.getElementById('flash-error'); if (el) el.remove(); }, 5000);
    </script>
</body>
</html>
