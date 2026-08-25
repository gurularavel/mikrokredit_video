@extends('layouts.admin')

@section('title', 'Tənzimləmələr')

@section('content')
<div class="page-header">
    <h2><span class="eyebrow">Konfiqurasiya</span>Tənzimləmələr</h2>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
    @csrf

    <div class="detail-card">
        <h3>Sosial şəbəkə önizləməsi — Open Graph</h3>
        <p class="settings-hint">WhatsApp, Telegram və digər platformalarda video linki paylaşıldıqda göstəriləcək məlumatlar.</p>

        <div class="form-group">
            <label for="og_title">Başlıq (og:title)</label>
            <input type="text" id="og_title" name="og_title" value="{{ \App\Services\SettingService::get('og_title') }}"
                   placeholder="Məs: Video Kredit Müraciəti" maxlength="255">
        </div>

        <div class="form-group">
            <label for="og_description">Açıqlama (og:description)</label>
            <textarea id="og_description" name="og_description" rows="3" maxlength="500"
                      placeholder="Məs: Kredit müraciətinizin video təsdiqi">{{ \App\Services\SettingService::get('og_description') }}</textarea>
        </div>

        <div class="form-group">
            <label for="og_image">Şəkil (og:image)</label>
            @php $currentImg = \App\Services\SettingService::get('og_image_url'); @endphp
            @if($currentImg)
            <div class="og-image-preview">
                <img src="{{ $currentImg }}" alt="OG Image">
                <span class="og-image-hint">Mövcud şəkil. Yeni fayl seçsəniz əvəz olunacaq.</span>
            </div>
            @endif
            <input type="file" id="og_image" name="og_image" accept="image/*">
            <span class="field-hint">Tövsiyə edilən ölçü: 1200×630 px. Maksimum 2 MB.</span>
        </div>

        <div class="form-group">
            <label for="og_image_url">və ya şəkil URL-i</label>
            <input type="url" id="og_image_url" name="og_image_url" value="{{ \App\Services\SettingService::get('og_image_url') }}"
                   placeholder="https://...">
            <span class="field-hint">Fayl yükləsəniz bu sahə nəzərə alınmaz.</span>
        </div>

        <button type="submit" class="btn btn-primary">Yadda saxla</button>
    </div>

    <div class="detail-card">
        <h3>Video ekranı altı mətn</h3>
        <p class="settings-hint">Müştəriyə göndərilən video çəkiliş ekranının altında göstəriləcək mətn. <code>&lt;br&gt;</code> ilə yeni sətir əlavə edə bilərsiniz.</p>

        <div class="form-group">
            <textarea name="record_bottom_note" rows="4"
                      placeholder="Məs: Əlaqə üçün: 012 000 00 00&lt;br&gt;İş saatları: 09:00–18:00">{{ \App\Services\SettingService::get('record_bottom_note') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Yadda saxla</button>
    </div>
</form>
@endsection
