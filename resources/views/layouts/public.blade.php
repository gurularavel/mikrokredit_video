<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#10161D">
    <title>@yield('title', 'Video Kredit Müraciəti')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..800;1,9..144,300..800&family=Instrument+Sans:wght@400..700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @stack('head')
</head>
<body class="public-layout @yield('body_class')">
    <div class="grain" aria-hidden="true"></div>

    <div class="shell">
        <header class="masthead">
            <a class="brand" href="{{ url('/') }}">
                <span class="brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="1.5" y="5" width="14" height="14" rx="3"/>
                        <path d="M15.5 10.5l6-3.5v10l-6-3.5z"/>
                    </svg>
                </span>
                <span>
                    <span class="brand-name">Video Kredit</span>
                    <span class="brand-sub">Təsdiq Sistemi</span>
                </span>
            </a>
            <span class="masthead-note">@yield('masthead_note', 'Təhlükəsiz bağlantı')</span>
        </header>

        <main class="container">
            @yield('content')
        </main>

        <footer class="colophon">
            <span>© {{ date('Y') }} Video Kredit — bütün hüquqlar qorunur</span>
            <span class="mono">AZ · {{ now()->format('d.m.Y') }}</span>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
