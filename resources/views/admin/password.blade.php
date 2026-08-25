@extends('layouts.admin')

@section('title', 'Şifrə dəyiş')

@section('content')
<div class="page-header">
    <h2><span class="eyebrow">Hesab</span>Şifrə dəyiş</h2>
</div>

<div class="pw-grid">
    <div class="detail-card">
        <h3>Yeni giriş məlumatları</h3>

        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.password.update') }}" id="pwForm">
            @csrf

            <div class="form-group">
                <label for="pw_current">Cari şifrə</label>
                <div class="pw-wrap">
                    <input type="password" id="pw_current" name="current_password" required autofocus autocomplete="current-password">
                    <button type="button" class="pw-eye" data-pw-toggle="pw_current" title="Göstər/gizlət" aria-label="Göstər/gizlət"></button>
                </div>
            </div>

            <div class="auth-rule" aria-hidden="true"></div>

            <div class="form-group">
                <label for="pw_new">Yeni şifrə</label>
                <div class="pw-wrap">
                    <input type="password" id="pw_new" name="password" required minlength="6" autocomplete="new-password">
                    <button type="button" class="pw-eye" data-pw-toggle="pw_new" title="Göstər/gizlət" aria-label="Göstər/gizlət"></button>
                </div>
                <div class="pw-meter" id="pwMeter" aria-hidden="true">
                    <span></span><span></span><span></span><span></span>
                </div>
                <span class="field-hint" id="pwMeterLabel">Ən azı 6 simvol.</span>
            </div>

            <div class="form-group">
                <label for="pw_confirm">Yeni şifrəni təkrarla</label>
                <div class="pw-wrap">
                    <input type="password" id="pw_confirm" name="password_confirmation" required minlength="6" autocomplete="new-password">
                    <button type="button" class="pw-eye" data-pw-toggle="pw_confirm" title="Göstər/gizlət" aria-label="Göstər/gizlət"></button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px">Yadda saxla</button>
        </form>
    </div>

    <div class="detail-card pw-aside">
        <h3>Tələblər</h3>

        <ul class="checklist" id="pwChecks">
            <li data-check="len">
                <span class="check-mark" aria-hidden="true"></span>
                <span>Ən azı 6 simvol</span>
            </li>
            <li data-check="mix">
                <span class="check-mark" aria-hidden="true"></span>
                <span>Hərf və rəqəm birlikdə</span>
            </li>
            <li data-check="case">
                <span class="check-mark" aria-hidden="true"></span>
                <span>Böyük və kiçik hərf</span>
            </li>
            <li data-check="match">
                <span class="check-mark" aria-hidden="true"></span>
                <span>Təkrar şifrə uyğundur</span>
            </li>
        </ul>

        <div class="auth-rule" aria-hidden="true"></div>

        <p class="settings-hint" style="margin:0">
            Şifrəni dəyişdikdən sonra cari sessiya davam edir, lakin digər cihazlarda yenidən giriş tələb oluna bilər.
            Şifrənizi heç kimlə paylaşmayın.
        </p>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var pw      = document.getElementById('pw_new');
    var confirm = document.getElementById('pw_confirm');
    var meter   = document.getElementById('pwMeter');
    var label   = document.getElementById('pwMeterLabel');
    var checks  = document.getElementById('pwChecks');
    if (!pw || !confirm) return;

    var LEVELS = ['Çox zəif', 'Zəif', 'Orta', 'Güclü'];

    function evaluate() {
        var v = pw.value;
        var state = {
            len:   v.length >= 6,
            mix:   /[A-Za-zÇÖĞİŞÜƏçöğışüə]/.test(v) && /\d/.test(v),
            case:  /[a-zçöğışüə]/.test(v) && /[A-ZÇÖĞİŞÜƏ]/.test(v),
            match: v.length > 0 && v === confirm.value
        };

        checks.querySelectorAll('[data-check]').forEach(function (li) {
            li.classList.toggle('ok', !!state[li.dataset.check]);
        });

        var score = 0;
        if (state.len)  score++;
        if (state.mix)  score++;
        if (state.case) score++;
        if (v.length >= 10) score++;

        meter.dataset.score = v.length ? score : 0;
        label.textContent = v.length ? 'Güc: ' + LEVELS[Math.max(score - 1, 0)] : 'Ən azı 6 simvol.';
    }

    pw.addEventListener('input', evaluate);
    confirm.addEventListener('input', evaluate);
    evaluate();
})();
</script>
@endpush
@endsection
