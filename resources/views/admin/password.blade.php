@extends('layouts.admin')

@section('title', 'Şifrə dəyiş')

@section('content')
<div class="page-header">
    <h2>Şifrə dəyiş</h2>
</div>

<div class="detail-card" style="max-width:480px">
    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.password.update') }}">
        @csrf
        <div class="form-group">
            <label>Cari şifrə</label>
            <div class="pw-wrap">
                <input type="password" id="pw_current" name="current_password" required autofocus>
                <button type="button" class="pw-eye" data-target="pw_current" title="Göstər/gizlət">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>
        <div class="form-group">
            <label>Yeni şifrə</label>
            <div class="pw-wrap">
                <input type="password" id="pw_new" name="password" required minlength="6">
                <button type="button" class="pw-eye" data-target="pw_new" title="Göstər/gizlət">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>
        <div class="form-group">
            <label>Yeni şifrəni təkrarla</label>
            <div class="pw-wrap">
                <input type="password" id="pw_confirm" name="password_confirmation" required minlength="6">
                <button type="button" class="pw-eye" data-target="pw_confirm" title="Göstər/gizlət">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Yadda saxla</button>
    </form>

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
</div>
@endsection
