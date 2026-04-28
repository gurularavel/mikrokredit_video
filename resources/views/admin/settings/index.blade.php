@extends('layouts.admin')

@section('title', 'Tənzimləmələr')

@section('content')
<div class="page-header">
    <h2>Tənzimləmələr</h2>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf

    <div class="detail-card">
        <h3>Sosial Şəbəkə Önizləməsi (Open Graph)</h3>
        <p class="settings-hint">WhatsApp, Telegram və digər platformalarda video linki paylaşıldıqda göstəriləcək məlumatlar.</p>

        <div class="form-group">
            <label>Başlıq (og:title)</label>
            <input type="text" name="og_title" value="{{ \App\Services\SettingService::get('og_title') }}"
                   placeholder="Məs: Video Kredit Müraciəti" maxlength="255">
        </div>

        <div class="form-group">
            <label>Açıqlama (og:description)</label>
            <textarea name="og_description" rows="3" maxlength="500"
                      placeholder="Məs: Kredit müraciətinizin video təsdiqi">{{ \App\Services\SettingService::get('og_description') }}</textarea>
        </div>

        <div class="form-group">
            <label>Şəkil (og:image)</label>
            @php $currentImg = \App\Services\SettingService::get('og_image_url'); @endphp
            @if($currentImg)
            <div class="og-image-preview">
                <img src="{{ $currentImg }}" alt="OG Image">
                <span class="og-image-hint">Mövcud şəkil. Yeni fayl seçsəniz əvəz olunacaq.</span>
            </div>
            @endif
            <input type="file" name="og_image" accept="image/*">
            <span class="field-hint">Tövsiyə edilən ölçü: 1200×630 px. Maks: 2 MB.</span>
        </div>

        <div class="form-group">
            <label>və ya Şəkil URL-i</label>
            <input type="url" name="og_image_url" value="{{ \App\Services\SettingService::get('og_image_url') }}"
                   placeholder="https://...">
            <span class="field-hint">Fayl yükləsəniz bu sahə nəzərə alınmaz.</span>
        </div>

        <button type="submit" class="btn btn-primary">Yadda saxla</button>
    </div>
</form>

<style>
.settings-hint {
    color: var(--color-text-secondary);
    font-size: .875rem;
    margin-bottom: 20px;
    margin-top: -4px;
}
.field-hint {
    display: block;
    font-size: .78rem;
    color: var(--color-text-muted, #94a3b8);
    margin-top: 5px;
}
.og-image-preview {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 10px;
}
.og-image-preview img {
    width: 160px;
    height: 84px;
    object-fit: cover;
    border-radius: 8px;
    border: 1.5px solid var(--color-border);
}
.og-image-hint {
    font-size: .8rem;
    color: var(--color-text-secondary);
}
</style>
@endsection
