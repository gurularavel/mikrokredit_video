<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş — Video Kredit</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
</head>
<body class="admin-login-page">
    <div class="login-card">
        <div class="login-header">
            <h1>Admin Panel</h1>
            <p>Video Kredit İdarəetmə</p>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" class="login-form">
            @csrf
            <div class="form-group">
                <label for="username">İstifadəçi adı</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" autofocus required>
            </div>
            <div class="form-group">
                <label for="password">Şifrə</label>
                <div class="pw-wrap">
                    <input type="password" id="password" name="password" required>
                    <button type="button" class="pw-eye" data-target="password" title="Şifrəni göstər/gizlət">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Daxil ol</button>
        </form>
    </div>
<script>
document.querySelectorAll('.pw-eye').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var input = document.getElementById(this.dataset.target);
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        this.innerHTML = show
            ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    });
});
</script>
</body>
</html>
