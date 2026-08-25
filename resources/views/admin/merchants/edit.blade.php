@extends('layouts.admin')

@section('title', 'Merchant Redaktəsi')

@section('content')
<div class="page-header">
    <a href="{{ route('admin.merchants.index') }}" class="btn btn-secondary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="11 18 5 12 11 6"/></svg>
        Geri
    </a>
    <h2><span class="eyebrow">Merchant № {{ $merchant->id }}</span>{{ $merchant->name }}</h2>
</div>

<div class="detail-card form-narrow">
    <h3>Merchant profili</h3>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.merchants.update', $merchant) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="form-group">
            <label for="m_name">Merchant adı <span class="req">*</span></label>
            <input type="text" id="m_name" name="name" value="{{ old('name', $merchant->name) }}" required maxlength="100">
        </div>

        <div class="form-group">
            <label for="m_login">Login <span class="req">*</span></label>
            <input type="text" id="m_login" name="login" value="{{ old('login', $merchant->login) }}" required maxlength="100"
                   pattern="[a-zA-Z0-9_\-]+" title="Yalnız hərf, rəqəm, _ və - istifadə edin">
        </div>

        <div class="form-group">
            <label for="auth_key_field">Auth Key</label>
            <div class="key-field">
                <input type="password" id="auth_key_field" value="{{ $merchant->auth_key }}" readonly>
                <button type="button" class="key-icon-btn" data-pw-toggle="auth_key_field" title="Göstər/gizlət" aria-label="Göstər/gizlət"></button>
                <button type="button" class="key-icon-btn" id="copy_key_btn" title="Kopyala" aria-label="Kopyala">
                    <svg class="icon-copy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    <svg class="icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:none;color:var(--moss)"><polyline points="20 6 9 17 4 12"/></svg>
                </button>
                <button type="button" class="btn btn-secondary btn-sm" id="gen_key_btn" title="Yeni key yarat">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Yeni key
                </button>
            </div>
            <span class="field-hint">Yeni key yaratdıqda köhnə key dərhal işləməyi dayandırır.</span>
        </div>

        <div class="form-group">
            <label for="logo_input">Logo</label>
            @if($merchant->logo)
                <div class="logo-preview" style="margin-bottom:10px">
                    <img src="{{ asset('storage/' . $merchant->logo) }}" alt="{{ $merchant->name }}">
                </div>
            @endif
            <input type="file" id="logo_input" name="logo" accept="image/*" onchange="previewLogo(this)">
            <div class="logo-preview" id="logo_preview" style="display:none;margin-top:10px">
                <img id="logo_img" src="" alt="Logo önizləmə">
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px">Yadda saxla</button>
    </form>
</div>

@push('scripts')
<script>
var generateUrl = '{{ route('admin.merchants.generate-key', $merchant) }}';
var csrfToken   = '{{ csrf_token() }}';

function previewLogo(input) {
    var preview = document.getElementById('logo_preview');
    var img = document.getElementById('logo_img');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('copy_key_btn').addEventListener('click', function () {
    var self  = this;
    var input = document.getElementById('auth_key_field');
    window.copyText(input.value).then(function () {
        var iconCopy  = self.querySelector('.icon-copy');
        var iconCheck = self.querySelector('.icon-check');
        iconCopy.style.display  = 'none';
        iconCheck.style.display = '';
        setTimeout(function () {
            iconCopy.style.display  = '';
            iconCheck.style.display = 'none';
        }, 1600);
    });
});

document.getElementById('gen_key_btn').addEventListener('click', function () {
    if (!confirm('Yeni auth key yaratmaq istədiyinizə əminsiniz? Köhnə key dərhal işləməyəcək.')) return;
    var btn  = this;
    var html = btn.innerHTML;
    btn.disabled = true;
    btn.textContent = '…';

    fetch(generateUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
        var input = document.getElementById('auth_key_field');
        input.value = data.auth_key;
        input.type  = 'text';
        btn.disabled = false;
        btn.innerHTML = html;
    })
    .catch(function () {
        alert('Xəta baş verdi.');
        btn.disabled = false;
        btn.innerHTML = html;
    });
});
</script>
@endpush
@endsection
