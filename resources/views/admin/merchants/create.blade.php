@extends('layouts.admin')

@section('title', 'Yeni Merchant')

@section('content')
<div class="page-header" style="display:flex;align-items:center;gap:12px">
    <a href="{{ route('admin.merchants.index') }}" class="btn btn-secondary btn-sm">&#8592; Geri</a>
    <h2>Yeni Merchant</h2>
</div>

<div class="detail-card" style="max-width:540px">
    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.merchants.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label>Merchant adı <span style="color:var(--danger,#e53e3e)">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required maxlength="100" autofocus>
        </div>

        <div class="form-group">
            <label>Login <span style="color:var(--danger,#e53e3e)">*</span></label>
            <input type="text" name="login" value="{{ old('login') }}" required maxlength="100"
                   pattern="[a-zA-Z0-9_\-]+" title="Yalnız hərf, rəqəm, _ və - istifadə edin">
            <small class="text-muted">API Basic Auth üçün istifadəçi adı</small>
        </div>

        <div class="form-group">
            <label>Logo</label>
            <input type="file" id="logo_input" name="logo" accept="image/*"
                   onchange="previewLogo(this)">
            <div id="logo_preview" style="margin-top:8px;display:none">
                <img id="logo_img" src="" alt="Logo preview"
                     style="width:80px;height:80px;object-fit:contain;border-radius:6px;background:var(--bg-hover)">
            </div>
        </div>

        <div class="form-group">
            <label>Auth Key</label>
            <div class="pw-wrap">
                <input type="password" id="auth_key_field" name="_auth_key_preview" readonly
                       value="{{ $generatedKey ?? '' }}"
                       placeholder="Saxla düyməsini basanda avtomatik yaranacaq"
                       style="font-family:monospace;font-size:12px">
                <button type="button" class="pw-eye" data-target="auth_key_field" title="Göstər/gizlət">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <small class="text-muted">Merchant yaradıldıqdan sonra auth key avtomatik təyin olunur. Redaktə səhifəsindən görə bilərsiniz.</small>
        </div>

        <button type="submit" class="btn btn-primary">Yarat</button>
    </form>
</div>

@push('scripts')
<script>
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
    } else {
        preview.style.display = 'none';
    }
}

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
@endpush
@endsection
