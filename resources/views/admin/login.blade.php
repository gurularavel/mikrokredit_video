<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş — Video Kredit</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
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
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Daxil ol</button>
        </form>
    </div>
</body>
</html>
