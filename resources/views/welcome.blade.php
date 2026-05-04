<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            height: 100%;
            background: #1b1b18;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        img {
            max-width: 180px;
            width: 100%;
        }
        p {
            margin-top: 16px;
            color: #706f6c;
            font-size: 14px;
            font-family: ui-sans-serif, system-ui, sans-serif;
            letter-spacing: 0.04em;
            text-align: center;
        }
    </style>
</head>
<body>
    <img src="{{ asset('image/logo.svg') }}" alt="{{ config('app.name') }}">
    <p>Video Qeydiyyat Sistemi</p>
</body>
</html>
