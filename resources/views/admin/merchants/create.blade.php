@extends('layouts.admin')

@section('title', 'Yeni Merchant')

@section('content')
<div class="page-header">
    <a href="{{ route('admin.merchants.index') }}" class="btn btn-secondary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="11 18 5 12 11 6"/></svg>
        Geri
    </a>
    <h2><span class="eyebrow">Merchantlər</span>Yeni merchant</h2>
</div>

<div class="detail-card form-narrow">
    <h3>Merchant profili</h3>

    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.merchants.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="m_name">Merchant adı <span class="req">*</span></label>
            <input type="text" id="m_name" name="name" value="{{ old('name') }}" required maxlength="100" autofocus>
        </div>

        <div class="form-group">
            <label for="m_login">Login <span class="req">*</span></label>
            <input type="text" id="m_login" name="login" value="{{ old('login') }}" required maxlength="100"
                   pattern="[a-zA-Z0-9_\-]+" title="Yalnız hərf, rəqəm, _ və - istifadə edin">
            <span class="field-hint">API Basic Auth üçün istifadəçi adı.</span>
        </div>

        <div class="form-group">
            <label for="logo_input">Logo</label>
            <input type="file" id="logo_input" name="logo" accept="image/*" onchange="previewLogo(this)">
            <div class="logo-preview" id="logo_preview" style="display:none;margin-top:10px">
                <img id="logo_img" src="" alt="Logo önizləmə">
            </div>
        </div>

        <div class="form-group">
            <label for="auth_key_field">Auth Key</label>
            <div class="pw-wrap">
                <input type="password" id="auth_key_field" name="_auth_key_preview" readonly
                       value="{{ $generatedKey ?? '' }}"
                       placeholder="Yaradıldıqdan sonra avtomatik təyin olunur"
                       style="font-family:var(--font-mono);font-size:.75rem">
                <button type="button" class="pw-eye" data-pw-toggle="auth_key_field" title="Göstər/gizlət" aria-label="Göstər/gizlət"></button>
            </div>
            <span class="field-hint">Merchant yaradıldıqdan sonra açar avtomatik təyin olunur — redaktə səhifəsindən görə bilərsiniz.</span>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px">Yarat</button>
    </form>
</div>

@push('scripts')
<script>
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
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endpush
@endsection
