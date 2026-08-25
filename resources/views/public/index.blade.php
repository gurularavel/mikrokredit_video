@extends('layouts.public')

@section('title', 'Video Kredit Müraciəti')

@section('content')
<div class="split">

    <section class="split-intro reveal reveal-1">
        <span class="eyebrow">Addım 01 — Məlumatlar</span>

        <h1 class="display">Kredit müraciətini <em>video</em> ilə təsdiqləyin.</h1>

        <p class="lede">
            Məlumatlarınızı daxil edin — telefon nömrənizə qısa video müraciət linki
            göndəriləcək. Bütün proses bir neçə dəqiqə çəkir.
        </p>

        <ul class="spec-list">
            <li>
                <span class="idx">01</span>
                <span>
                    <strong>Formu doldurun</strong>
                    <span>Ad, soyad və mobil nömrə kifayətdir.</span>
                </span>
            </li>
            <li>
                <span class="idx">02</span>
                <span>
                    <strong>SMS linkini açın</strong>
                    <span>Link yalnız bir dəfə və məhdud müddət ərzində işləyir.</span>
                </span>
            </li>
            <li>
                <span class="idx">03</span>
                <span>
                    <strong>Qısa video çəkin</strong>
                    <span>Ekranda görünən mətni oxuyun — cəmi {{ (int) config('video.duration', 20) }} saniyə.</span>
                </span>
            </li>
        </ul>
    </section>

    <section class="paper reveal reveal-2">
        <div class="paper-head">
            <h2>Müraciət formu</h2>
            <span class="stamp-no">№ {{ strtoupper(substr(md5(date('Ymd')), 0, 6)) }}</span>
        </div>

        @if($errors->any())
            <div class="alert alert-error">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('applications.store') }}" class="application-form">
            @csrf
            <div class="field">
                <label for="name">Ad</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Adınız" required autocomplete="given-name">
            </div>
            <div class="field">
                <label for="surname">Soyad</label>
                <input type="text" id="surname" name="surname" value="{{ old('surname') }}" placeholder="Soyadınız" required autocomplete="family-name">
            </div>
            <div class="field">
                <label for="phone">Telefon nömrəsi</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+994501234567" maxlength="13" required autocomplete="tel" inputmode="numeric">
                <span class="field-hint">Linki bu nömrəyə SMS ilə göndərəcəyik.</span>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:24px">
                SMS göndər
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="13 6 19 12 13 18"/></svg>
            </button>
        </form>
    </section>

</div>

@push('scripts')
<script>
(function () {
    var PREFIX = '+994';
    var input = document.getElementById('phone');
    if (!input) return;

    function enforce(input) {
        var val = input.value;
        if (!val.startsWith(PREFIX)) {
            var digits = val.replace(/\D/g, '');
            if (digits.startsWith('994')) digits = digits.slice(3);
            val = PREFIX + digits;
        }
        var suffix = val.slice(PREFIX.length).replace(/\D/g, '').slice(0, 9);
        input.value = PREFIX + suffix;
    }

    input.addEventListener('focus', function () {
        if (!this.value.startsWith(PREFIX)) this.value = PREFIX;
        var len = this.value.length;
        this.setSelectionRange(len, len);
    });

    input.addEventListener('input', function () { enforce(this); });

    input.addEventListener('keydown', function (e) {
        var start = this.selectionStart;
        if (start <= PREFIX.length && (e.key === 'Backspace' || e.key === 'Delete')) {
            e.preventDefault();
        }
    });

    // set initial value if pre-filled
    if (input.value) enforce(input);
})();
</script>
@endpush
@endsection
