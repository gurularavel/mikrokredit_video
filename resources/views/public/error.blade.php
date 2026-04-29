@extends('layouts.public')

@section('title', 'Xəta')

@section('content')
<div class="status-card">
    <div class="status-icon error">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
    </div>
    @if($reason === 'expired')
        <h2>{{ \App\Services\TemplateService::render('page_error_expired_title', [], 'Link müddəti bitib') }}</h2>
        <p>{{ \App\Services\TemplateService::render('page_error_expired_body', [], 'Bu link artıq etibarlı deyil. Yeni müraciət etmək üçün formu doldurun.') }}</p>
    @elseif($reason === 'used')
        <h2>{{ \App\Services\TemplateService::render('page_error_used_title', [], 'Link artıq istifadə edilib') }}</h2>
        <p>{{ \App\Services\TemplateService::render('page_error_used_body', [], 'Bu link yalnız bir dəfə istifadə edilə bilər. Video artıq göndərilmişdir.') }}</p>
    @else
        <h2>{{ \App\Services\TemplateService::render('page_error_notfound_title', [], 'Link tapılmadı') }}</h2>
        <p>{{ \App\Services\TemplateService::render('page_error_notfound_body', [], 'Belə bir link mövcud deyil. Zəhmət olmasa linki yoxlayın.') }}</p>
    @endif
    <a href="{{ url('/') }}" class="btn btn-secondary">Ana Səhifəyə Qayıt</a>
</div>
@endsection
