@extends('layouts.public')

@section('title', 'Xəta')

@section('content')
<div class="receipt">
    <div class="stamp stamp-error reveal reveal-1" aria-hidden="true">
        @if($reason === 'expired')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 14"/>
            </svg>
        @elseif($reason === 'used')
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>
            </svg>
        @else
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9"/><line x1="12" y1="7.5" x2="12" y2="13"/><line x1="12" y1="16.5" x2="12.01" y2="16.5"/>
            </svg>
        @endif
    </div>

    @if($reason === 'expired')
        <h1 class="reveal reveal-2">{{ \App\Services\TemplateService::render('page_error_expired_title', [], 'Link müddəti bitib') }}</h1>
        <p class="reveal reveal-2">{{ \App\Services\TemplateService::render('page_error_expired_body', [], 'Bu link artıq etibarlı deyil. Yeni müraciət etmək üçün formu doldurun.') }}</p>
    @elseif($reason === 'used')
        <h1 class="reveal reveal-2">{{ \App\Services\TemplateService::render('page_error_used_title', [], 'Link artıq istifadə edilib') }}</h1>
        <p class="reveal reveal-2">{{ \App\Services\TemplateService::render('page_error_used_body', [], 'Bu link yalnız bir dəfə istifadə edilə bilər. Video artıq göndərilmişdir.') }}</p>
    @else
        <h1 class="reveal reveal-2">{{ \App\Services\TemplateService::render('page_error_notfound_title', [], 'Link tapılmadı') }}</h1>
        <p class="reveal reveal-2">{{ \App\Services\TemplateService::render('page_error_notfound_body', [], 'Belə bir link mövcud deyil. Zəhmət olmasa linki yoxlayın.') }}</p>
    @endif

    <div class="perforation reveal reveal-3" aria-hidden="true"></div>

    <div class="receipt-actions reveal reveal-4">
        <a href="{{ url('/') }}" class="btn btn-secondary">Ana səhifəyə qayıt</a>
    </div>
</div>
@endsection
