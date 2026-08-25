<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0B1117">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Giriş — Video Kredit</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..700;1,9..144,400..700&family=Instrument+Sans:wght@400..700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    <script>if(localStorage.getItem('adminTheme')==='dark')document.documentElement.classList.add('dark');</script>
</head>
<body class="auth-page">
<div class="auth-split">

    {{-- Left: ink panel --}}
    <aside class="auth-aside">
        <a class="auth-brand" href="{{ url('/') }}">
            <span class="auth-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.1" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1.5" y="5" width="14" height="14" rx="3"/>
                    <path d="M15.5 10.5l6-3.5v10l-6-3.5z"/>
                </svg>
            </span>
            <span>
                <span class="auth-brand-name">Video Kredit</span>
                <span class="auth-brand-sub">Təsdiq Sistemi</span>
            </span>
        </a>

        <div class="auth-statement">
            <span class="auth-eyebrow">İdarəetmə paneli</span>
            <h2>Müraciət, SMS və video arxivi — <em>bir</em> konsolda.</h2>
            <p>Müraciətləri izləyin, təsdiq linklərini yenidən göndərin və çəkilmiş videoları nəzərdən keçirin.</p>
        </div>

        <dl class="auth-facts">
            <div>
                <dt>Sistem</dt>
                <dd class="ok">Aktiv</dd>
            </div>
            <div>
                <dt>Link müddəti</dt>
                <dd>{{ (int) config('sms.expiry_minutes', 30) }} dəq</dd>
            </div>
            <div>
                <dt>Video limiti</dt>
                <dd>{{ (int) config('video.duration', 20) }} san</dd>
            </div>
        </dl>
    </aside>

    {{-- Right: form panel --}}
    <main class="auth-main">
        <button type="button" class="auth-theme" id="themeToggle" title="Dark/Light mode" aria-label="Mövzunu dəyiş">
            <svg id="themeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
            </svg>
        </button>

        <div class="auth-card">
            <header class="auth-head">
                <span class="auth-eyebrow">Giriş</span>
                <h1>Xoş gəldiniz</h1>
                <p>Davam etmək üçün hesab məlumatlarınızı daxil edin.</p>
            </header>

            @if($errors->any())
                <div class="alert alert-error">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                <div class="form-group">
                    <label for="username">İstifadəçi adı</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" autofocus required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">Şifrə</label>
                    <div class="pw-wrap">
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                        <button type="button" class="pw-eye" data-pw-toggle="password" title="Şifrəni göstər/gizlət" aria-label="Şifrəni göstər/gizlət"></button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top:22px">
                    Daxil ol
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="13 6 19 12 13 18"/></svg>
                </button>
            </form>

            <div class="auth-rule" aria-hidden="true"></div>

            <p class="auth-foot">
                Şifrənizi unutmusunuzsa, sistem administratoru ilə əlaqə saxlayın.<br>
                <span class="mono">© {{ date('Y') }} VIDEO KREDIT</span>
            </p>
        </div>
    </main>

</div>

<script>
(function () {
    /* ── Password visibility ── */
    var EYE = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
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

    /* ── Dark / Light theme ── */
    var html      = document.documentElement;
    var themeBtn  = document.getElementById('themeToggle');
    var themeIcon = document.getElementById('themeIcon');

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
        html.classList.toggle('dark', dark);
        themeIcon.innerHTML = dark ? SUN : MOON;
        themeBtn.title = dark ? 'Light mode' : 'Dark mode';
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
