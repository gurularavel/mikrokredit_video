<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — Video Kredit</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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
            <a href="{{ route('admin.templates.index') }}" class="{{ request()->routeIs('admin.templates.*') ? 'active' : '' }}" title="Mesaj Şablonları">
                <span class="nav-icon">&#9998;</span>
                <span class="nav-label">Mesaj Şablonları</span>
            </a>
            <a href="{{ route('admin.password') }}" class="{{ request()->routeIs('admin.password*') ? 'active' : '' }}" title="Şifrə dəyiş">
                <span class="nav-icon">&#128274;</span>
                <span class="nav-label">Şifrə dəyiş</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <span class="nav-label">{{ session('admin_user.name') }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" title="Çıxış">&#10148;</button>
            </form>
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

    <script>
        (function () {
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
        })();
    </script>
</body>
</html>
