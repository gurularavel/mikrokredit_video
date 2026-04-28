@extends('layouts.admin')

@section('title', 'Merchant Redaktəsi')

@section('content')
<div class="page-header" style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('admin.merchants.index') }}" class="btn btn-secondary btn-sm">&#8592; Geri</a>
    <h2>{{ $merchant->name }}</h2>
</div>

<div class="detail-card" style="max-width:540px">
    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.merchants.update', $merchant) }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="form-group">
            <label>Merchant adı <span style="color:var(--danger,#e53e3e)">*</span></label>
            <input type="text" name="name" value="{{ old('name', $merchant->name) }}" required maxlength="100">
        </div>

        <div class="form-group">
            <label>Login <span style="color:var(--danger,#e53e3e)">*</span></label>
            <input type="text" name="login" value="{{ old('login', $merchant->login) }}" required maxlength="100"
                   pattern="[a-zA-Z0-9_\-]+" title="Yalnız hərf, rəqəm, _ və - istifadə edin">
        </div>

        <div class="form-group">
            <label>Auth Key</label>
            <div class="pw-wrap">
                <input type="password" id="auth_key_field" value="{{ $merchant->auth_key }}" readonly
                       style="font-family:monospace;font-size:12px">
                <button type="button" class="pw-eye" data-target="auth_key_field" title="Göstər/gizlət">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
                <button type="button" class="pw-eye copy-btn" id="copy_key_btn" title="Kopyala">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                </button>
                <button type="button" class="btn btn-secondary btn-sm" id="gen_key_btn" title="Yeni key yarat"
                        style="flex-shrink:0;white-space:nowrap">&#128260; Yeni key</button>
            </div>
            <small class="text-muted">Yeni key yaratdıqda köhnə key dərhal işləməyi dayandırır.</small>
        </div>

        <div class="form-group">
            <label>Logo</label>
            @if($merchant->logo)
                <div style="margin-bottom:8px">
                    <img src="{{ asset('storage/' . $merchant->logo) }}" alt="{{ $merchant->name }}"
                         style="width:80px;height:80px;object-fit:contain;border-radius:6px;background:var(--bg-hover)">
                </div>
            @endif
            <input type="file" id="logo_input" name="logo" accept="image/*"
                   onchange="previewLogo(this)">
            <div id="logo_preview" style="margin-top:8px;display:none">
                <img id="logo_img" src="" alt="Logo preview"
                     style="width:80px;height:80px;object-fit:contain;border-radius:6px;background:var(--bg-hover)">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Yadda saxla</button>
    </form>
</div>

@push('scripts')
<script>
var merchantId = {{ $merchant->id }};
var generateUrl = '{{ route('admin.merchants.generate-key', $merchant) }}';
var csrfToken = '{{ csrf_token() }}';

function previewLogo(input) {
    var preview = document.getElementById('logo_preview');
    var img = document.getElementById('logo_img');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.querySelectorAll('.pw-eye').forEach(function(btn) {
    if (btn.id === 'copy_key_btn') return;
    btn.addEventListener('click', function() {
        var input = document.getElementById(this.dataset.target);
        if (!input) return;
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        this.innerHTML = show
            ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    });
});

document.getElementById('copy_key_btn').addEventListener('click', function() {
    var input = document.getElementById('auth_key_field');
    navigator.clipboard.writeText(input.value).then(function() {
        var btn = document.getElementById('copy_key_btn');
        var original = btn.innerHTML;
        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
        setTimeout(function() { btn.innerHTML = original; }, 1500);
    });
});

document.getElementById('gen_key_btn').addEventListener('click', function() {
    if (!confirm('Yeni auth key yaratmaq istədiyinizə əminsiniz? Köhnə key dərhal işləməyəcək.')) return;
    var btn = this;
    btn.disabled = true;
    btn.textContent = '...';

    fetch(generateUrl, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var input = document.getElementById('auth_key_field');
        input.value = data.auth_key;
        input.type = 'text';
        document.getElementById('copy_key_btn').dataset && (document.getElementById('copy_key_btn')._key = data.auth_key);
        btn.disabled = false;
        btn.innerHTML = '&#128260; Yeni key';
    })
    .catch(function() {
        alert('Xəta baş verdi.');
        btn.disabled = false;
        btn.innerHTML = '&#128260; Yeni key';
    });
});
</script>
@endpush
@endsection
