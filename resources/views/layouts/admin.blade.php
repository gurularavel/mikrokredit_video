<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — Video Kredit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script>if(localStorage.getItem('adminTheme')==='dark')document.documentElement.classList.add('dark');</script>
    @stack('head')
</head>
<body class="admin-layout">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <span class="brand-text">Video Kredit</span>
            <button class="sidebar-toggle" id="sidebarToggle" title="Sidebarı yığ/aç">&#9776;</button>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.applications.index') }}" class="{{ request()->routeIs('admin.applications.*') ? 'active' : '' }}" title="Müraciətlər">
                <span class="nav-icon">&#128196;</span>
                <span class="nav-label">Müraciətlər</span>
            </a>
            <a href="{{ route('admin.sms-logs.index') }}" class="{{ request()->routeIs('admin.sms-logs.*') ? 'active' : '' }}" title="SMS Logları">
                <span class="nav-icon">&#128241;</span>
                <span class="nav-label">SMS Logları</span>
            </a>
            <a href="{{ route('admin.merchants.index') }}" class="{{ request()->routeIs('admin.merchants.*') ? 'active' : '' }}" title="Merchantlər">
                <span class="nav-icon">&#127978;</span>
                <span class="nav-label">Merchantlər</span>
            </a>
            <a href="{{ route('admin.templates.index') }}" class="{{ request()->routeIs('admin.templates.*') ? 'active' : '' }}" title="Mesaj Şablonları">
                <span class="nav-icon">&#9998;</span>
                <span class="nav-label">Mesaj Şablonları</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" title="Tənzimləmələr">
                <span class="nav-icon">&#9881;</span>
                <span class="nav-label">Tənzimləmələr</span>
            </a>
            <a href="{{ route('admin.password') }}" class="{{ request()->routeIs('admin.password*') ? 'active' : '' }}" title="Şifrə dəyiş">
                <span class="nav-icon">&#128274;</span>
                <span class="nav-label">Şifrə dəyiş</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <span class="nav-label">{{ session('admin_user.name') }}</span>
            <div style="display:flex;gap:6px;flex-shrink:0">
                <button type="button" class="theme-toggle" id="themeToggle" title="Dark/Light mode">
                    <svg id="themeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                </button>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" title="Çıxış">&#10148;</button>
                </form>
            </div>
        </div>
    </aside>
    <main class="main-content" id="mainContent">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/az.js"></script>
    @stack('scripts')
    <script>
        (function () {
            /* ── Sidebar collapse ── */
            var sidebar = document.getElementById('sidebar');
            var main    = document.getElementById('mainContent');
            var btn     = document.getElementById('sidebarToggle');

            if (localStorage.getItem('sidebarCollapsed') === '1') {
                sidebar.classList.add('collapsed');
                main.classList.add('sidebar-collapsed');
            }

            btn.addEventListener('click', function () {
                var collapsed = sidebar.classList.toggle('collapsed');
                main.classList.toggle('sidebar-collapsed', collapsed);
                localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
            });

            /* ── Dark / Light theme ── */
            var html       = document.documentElement;
            var themeBtn   = document.getElementById('themeToggle');
            var themeIcon  = document.getElementById('themeIcon');

            var MOON = '<path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>';
            var SUN  = '<circle cx="12" cy="12" r="5"/>'
                     + '<line x1="12" y1="1" x2="12" y2="3"/>'
                     + '<line x1="12" y1="21" x2="12" y2="23"/>'
                     + '<line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>'
                     + '<line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>'
                     + '<line x1="1" y1="12" x2="3" y2="12"/>'
                     + '<line x1="21" y1="12" x2="23" y2="12"/>'
                     + '<line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>'
                     + '<line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';

            function applyTheme(dark) {
                if (dark) {
                    html.classList.add('dark');
                    themeIcon.innerHTML = SUN;
                    themeBtn.title = 'Light mode';
                } else {
                    html.classList.remove('dark');
                    themeIcon.innerHTML = MOON;
                    themeBtn.title = 'Dark mode';
                }
            }

            applyTheme(localStorage.getItem('adminTheme') === 'dark');

            themeBtn.addEventListener('click', function () {
                var isDark = !html.classList.contains('dark');
                localStorage.setItem('adminTheme', isDark ? 'dark' : 'light');
                applyTheme(isDark);
            });
        })();
    </script>
</body>
</html>
