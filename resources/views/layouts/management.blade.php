<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Quản trị - Bách Hóa Xanh')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .pw-toggle { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; user-select: none; color: #6b7280; }
        .pw-toggle:hover { color: #374151; }
        .pw-wrap { position: relative; }

        /* Sidebar */
        .sidebar-section { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: rgba(255,255,255,0.45); padding: 0 12px; margin-bottom: 8px; }
        .sidebar-link { display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 8px; font-size: 0.85rem; font-weight: 500; color: rgba(255,255,255,0.82); transition: all 0.15s ease; }
        .sidebar-link i:not(.submenu-chevron) { font-size: 1rem; width: 20px; text-align: center; flex-shrink: 0; }
        .sidebar-link:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .sidebar-link.active { background: rgba(255,255,255,0.16); color: #fff; font-weight: 600; }
        .sidebar-sublink { display: block; padding: 7px 12px; border-radius: 7px; font-size: 0.8rem; color: rgba(255,255,255,0.7); transition: all 0.15s ease; }
        .sidebar-sublink:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .sidebar-sublink.active { background: rgba(255,193,7,0.18); color: #ffc107; font-weight: 600; }

        /* User dropdown */
        .user-dd { position: relative; }
        .user-dd-menu { position: absolute; right: 0; top: calc(100% + 6px); background: #fff; border-radius: 10px; box-shadow: 0 6px 24px rgba(0,0,0,0.12); min-width: 200px; opacity: 0; visibility: hidden; transform: translateY(-6px); transition: all 0.18s ease; z-index: 999; overflow: hidden; }
        .user-dd-menu.show { opacity: 1; visibility: visible; transform: translateY(0); }
        .user-dd-menu a, .user-dd-menu button { display: flex; align-items: center; gap: 8px; width: 100%; padding: 9px 14px; font-size: 0.8125rem; color: #374151; transition: background 0.12s ease; text-align: left; border: none; background: none; cursor: pointer; }
        .user-dd-menu a:hover, .user-dd-menu button:hover { background: #f3f4f6; }
        .user-dd-menu .dd-divider { height: 1px; background: #e5e7eb; margin: 4px 0; }

        /* ===== Management shell: sidebar + content (CSS fallback viết tay,
           luôn đúng kể cả khi Tailwind build thiếu class) ===== */
        .mgmt-shell { display: flex; align-items: stretch; flex: 1 1 auto; width: 100%; min-height: 0; }
        .mgmt-sidebar { width: 16rem; flex-shrink: 0; background: #064f30; color: #fff; display: flex; flex-direction: column; }
        .mgmt-nav { flex: 1 1 auto; min-height: 0; overflow-y: auto; }
        .mgmt-content { flex: 1 1 auto; min-width: 0; display: flex; flex-direction: column; }
        @media (min-width: 1024px) {
            .mgmt-sidebar { position: sticky; top: 4rem; height: calc(100vh - 4rem); }
            #sidebarOverlay { display: none !important; }
        }
        @media (max-width: 1023.98px) {
            .mgmt-sidebar { position: fixed; top: 4rem; bottom: 0; left: 0; z-index: 40; box-shadow: 0 10px 30px rgba(0,0,0,0.35); transform: translateX(-100%); transition: transform 0.2s ease; }
            .mgmt-sidebar.open { transform: none; }
        }
    </style>
</head>
<body class="bg-bhx-gray min-h-screen flex flex-col">
    {{-- ===== Topbar gọn cho trang quản trị ===== --}}
    <header class="sticky top-0 z-50 bg-bhx-700 text-white shadow">
        <div class="px-4 h-16 flex items-center gap-3">
            {{-- Hamburger mở sidebar trên mobile --}}
            <button onclick="toggleManagementSidebar()" class="lg:hidden text-white text-lg focus:outline-none p-2 rounded-lg hover:bg-white/10 transition" aria-label="Mở menu quản trị" aria-controls="managementSidebar" aria-expanded="false" id="sidebarToggle">
                <i class="bi bi-list" id="sidebarToggleIcon"></i>
            </button>

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <span class="w-9 h-9 rounded-lg bg-white flex items-center justify-center text-bhx-600 shadow-sm">
                    <i class="bi bi-basket text-xl"></i>
                </span>
                <span class="leading-none hidden sm:block">
                    <span class="block text-base font-extrabold tracking-tight">BÁCH HÓA XANH</span>
                    <span class="block text-[10px] text-bhx-100/90 mt-0.5 tracking-wide">Khu vực quản trị</span>
                </span>
            </a>

            <div class="flex items-center gap-2 ml-auto shrink-0">
                <a href="{{ route('home') }}" class="hidden md:inline-flex items-center gap-1.5 text-xs text-white/85 hover:text-white hover:bg-white/10 px-3 py-1.5 rounded-lg transition">
                    <i class="bi bi-house-door"></i> Về cửa hàng
                </a>
                @auth
                    <div class="user-dd" id="userDropdown">
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
                            <a href="{{ route('profile') }}"><i class="bi bi-person text-gray-400"></i> Thông tin cá nhân</a>
                            <a href="{{ route('change-password') }}"><i class="bi bi-key text-gray-400"></i> Đổi mật khẩu</a>
                            <div class="dd-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"><i class="bi bi-box-arrow-right text-gray-400"></i> Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </header>

    <div class="mgmt-shell">
        {{-- Overlay cho mobile --}}
        <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black/50 z-40 lg:hidden" onclick="toggleManagementSidebar()"></div>

        {{-- ===== Sidebar (desktop: nằm trong luồng trang, sticky; mobile: off-canvas) ===== --}}
        <aside id="managementSidebar" class="mgmt-sidebar w-64 text-white">
            <div class="px-4 pt-4 pb-2 flex items-center justify-between lg:hidden">
                <span class="text-xs font-bold uppercase tracking-wider text-white/50">Menu quản trị</span>
                <button onclick="toggleManagementSidebar()" class="text-white/70 hover:text-white p-1" aria-label="Đóng menu">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            @include('layouts.partials.sidebar')

            <div class="p-3 border-t border-white/10">
                <a href="{{ route('home') }}" class="sidebar-link">
                    <i class="bi bi-house-door"></i>
                    <span>Về cửa hàng</span>
                </a>
            </div>
        </aside>

        {{-- ===== Content ===== --}}
        <div class="mgmt-content flex-1">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="px-4 md:px-6 mt-4 w-full" id="flash-success">
                    <div class="bg-bhx-50 border border-bhx-400 text-bhx-800 px-4 py-3 rounded-lg flex justify-between items-center">
                        <span class="flex items-center gap-2"><i class="bi bi-check-circle-fill text-bhx-500"></i> {{ session('success') }}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-bhx-800 hover:text-bhx-900 font-bold text-lg leading-none">&times;</button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="px-4 md:px-6 mt-4 w-full" id="flash-error">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex justify-between items-center">
                        <span class="flex items-center gap-2"><i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}</span>
                        <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900 font-bold text-lg leading-none">&times;</button>
                    </div>
                </div>
            @endif

            <main class="px-4 md:px-6 py-6 flex-1 w-full min-w-0">
                @yield('content')
            </main>

            <footer class="border-t border-gray-200 bg-white">
                <div class="px-4 md:px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-2 text-gray-400 text-xs">
                    <span>&copy; {{ date('Y') }} Bách Hóa Xanh. Khu vực quản trị.</span>
                    <span>Thực phẩm tươi ngon &bull; Giá tốt mỗi ngày</span>
                </div>
            </footer>
        </div>
    </div>

    {{-- Delete confirm modal (dùng chung cho các form data-confirm) --}}
    <div id="deleteConfirmModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeDeleteConfirm()"></div>
        <div class="relative bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
            <h3 class="font-bold text-lg text-gray-800 mb-2">Xác nhận xóa</h3>
            <p id="deleteConfirmMessage" class="text-sm text-gray-600 mb-5"></p>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeDeleteConfirm()" class="px-4 py-2 rounded-lg text-sm border border-gray-300 text-gray-700 hover:bg-gray-100 transition">Hủy</button>
                <button type="button" id="deleteConfirmBtn" class="px-4 py-2 rounded-lg text-sm bg-red-600 text-white font-medium hover:bg-red-700 transition">Xóa</button>
            </div>
        </div>
    </div>

    <script>
        let deleteConfirmForm = null;

        function toggleManagementSidebar(force) {
            const sidebar = document.getElementById('managementSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const icon = document.getElementById('sidebarToggleIcon');
            const button = document.getElementById('sidebarToggle');
            if (!sidebar || !overlay) return;
            const shouldOpen = typeof force === 'boolean' ? force : !sidebar.classList.contains('open');
            sidebar.classList.toggle('open', shouldOpen);
            // Tương thích với class Tailwind translate (khi CSS build đầy đủ)
            sidebar.classList.toggle('-translate-x-full', !shouldOpen);
            overlay.classList.toggle('hidden', !shouldOpen);
            if (icon) icon.className = shouldOpen ? 'bi bi-x-lg' : 'bi bi-list';
            if (button) button.setAttribute('aria-expanded', String(shouldOpen));
        }

        function toggleSidebarSubmenu(id, btn) {
            const submenu = document.getElementById(id);
            if (!submenu) return;
            submenu.classList.toggle('hidden');
            const chevron = btn ? btn.querySelector('.submenu-chevron') : null;
            if (chevron) chevron.classList.toggle('rotate-180');
            if (btn) btn.setAttribute('aria-expanded', String(!submenu.classList.contains('hidden')));
        }

        function toggleUserDropdown() {
            document.getElementById('userDropdownMenu').classList.toggle('show');
        }

        document.addEventListener('click', function(e) {
            const dd = document.getElementById('userDropdown');
            const menu = document.getElementById('userDropdownMenu');
            if (dd && menu && !dd.contains(e.target)) {
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

        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form && form.matches && form.matches('form[data-confirm]')) {
                e.preventDefault();
                deleteConfirmForm = form;
                document.getElementById('deleteConfirmMessage').textContent = form.getAttribute('data-confirm');
                document.getElementById('deleteConfirmModal').classList.remove('hidden');
            }
        });

        function closeDeleteConfirm() {
            const modal = document.getElementById('deleteConfirmModal');
            if (modal) modal.classList.add('hidden');
            deleteConfirmForm = null;
        }

        document.getElementById('deleteConfirmBtn').addEventListener('click', function() {
            if (deleteConfirmForm) deleteConfirmForm.submit();
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteConfirm();
                const sidebar = document.getElementById('managementSidebar');
                if (sidebar && sidebar.classList.contains('open') && window.innerWidth < 1024) {
                    toggleManagementSidebar(false);
                }
            }
        });
    </script>
</body>
</html>
