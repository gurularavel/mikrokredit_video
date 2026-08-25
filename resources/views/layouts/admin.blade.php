<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — Video Kredit</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400..700&family=Instrument+Sans:wght@400..700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script>if(localStorage.getItem('adminTheme')==='dark')document.documentElement.classList.add('dark');</script>
    @stack('head')
</head>
<body class="admin-layout">
    @php
        $adminName = session('admin_user.name', 'Admin');
        $initials  = mb_strtoupper(mb_substr($adminName, 0, 1));
    @endphp

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <span class="sidebar-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1.5" y="5" width="14" height="14" rx="3"/>
                    <path d="M15.5 10.5l6-3.5v10l-6-3.5z"/>
                </svg>
            </span>
            <span class="brand-text">Video Kredit</span>
            <button class="sidebar-toggle" id="sidebarToggle" title="Sidebarı yığ/aç" aria-label="Sidebarı yığ/aç">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/>
                </svg>
            </button>
        </div>

        <nav class="sidebar-nav">
            <span class="nav-section">Əməliyyat</span>

            <a href="{{ route('admin.applications.index') }}" class="{{ request()->routeIs('admin.applications.*') ? 'active' : '' }}" title="Müraciətlər">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/>
                    </svg>
                </span>
                <span class="nav-label">Müraciətlər</span>
            </a>

            <a href="{{ route('admin.sms-logs.index') }}" class="{{ request()->routeIs('admin.sms-logs.*') ? 'active' : '' }}" title="SMS Logları">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
                    </svg>
                </span>
                <span class="nav-label">SMS Logları</span>
            </a>

            <a href="{{ route('admin.merchants.index') }}" class="{{ request()->routeIs('admin.merchants.*') ? 'active' : '' }}" title="Merchantlər">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l1.5-5h15L21 9"/><path d="M4 9v11h16V9"/><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/><path d="M10 20v-5h4v5"/>
                    </svg>
                </span>
                <span class="nav-label">Merchantlər</span>
            </a>

            <span class="nav-section">Konfiqurasiya</span>

            <a href="{{ route('admin.templates.index') }}" class="{{ request()->routeIs('admin.templates.*') ? 'active' : '' }}" title="Mesaj Şablonları">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/>
                    </svg>
                </span>
                <span class="nav-label">Mesaj Şablonları</span>
            </a>

            <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" title="Tənzimləmələr">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                </span>
                <span class="nav-label">Tənzimləmələr</span>
            </a>

            <a href="{{ route('admin.password') }}" class="{{ request()->routeIs('admin.password*') ? 'active' : '' }}" title="Şifrə dəyiş">
                <span class="nav-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </span>
                <span class="nav-label">Şifrə dəyiş</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <span class="sidebar-user">
                <span class="sidebar-avatar" aria-hidden="true">{{ $initials }}</span>
                <span class="nav-label">{{ $adminName }}</span>
            </span>
            <span class="sidebar-actions">
                <button type="button" class="theme-toggle" id="themeToggle" title="Dark/Light mode" aria-label="Mövzunu dəyiş">
                    <svg id="themeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                    </svg>
                </button>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="icon-btn danger" title="Çıxış" aria-label="Çıxış">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </button>
                </form>
            </span>
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

        /* ── Shared: password visibility toggles ── */
        (function () {
            var EYE  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
            var EYE_OFF = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';

            document.querySelectorAll('[data-pw-toggle]').forEach(function (btn) {
                btn.innerHTML = EYE;
                btn.addEventListener('click', function () {
                    var input = document.getElementById(this.getAttribute('data-pw-toggle'));
                    if (!input) return;
                    var show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    this.innerHTML = show ? EYE_OFF : EYE;
                });
            });
        })();

        /* ── Shared: copy to clipboard ── */
        window.copyText = function (text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            }
            var el = document.createElement('textarea');
            el.value = text;
            el.style.cssText = 'position:fixed;left:-9999px;top:-9999px;opacity:0';
            document.body.appendChild(el);
            el.focus();
            el.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(el);
            return Promise.resolve();
        };

        (function () {
            document.querySelectorAll('[data-copy]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var self = this;
                    window.copyText(this.dataset.copy).then(function () {
                        var iconCopy  = self.querySelector('.icon-copy');
                        var iconCheck = self.querySelector('.icon-check');
                        if (!iconCopy || !iconCheck) return;
                        iconCopy.style.display  = 'none';
                        iconCheck.style.display = '';
                        setTimeout(function () {
                            iconCopy.style.display  = '';
                            iconCheck.style.display = 'none';
                        }, 1600);
                    });
                });
            });
        })();
    </script>
</body>
</html>
