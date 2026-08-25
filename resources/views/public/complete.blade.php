@extends('layouts.public')

@section('title', 'Müraciət Tamamlandı')
@section('masthead_note', 'Addım 03 — Tamamlandı')

@section('content')
<div class="receipt">
    <div class="stamp stamp-success reveal reveal-1" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </div>

    <h1 class="reveal reveal-2">{{ \App\Services\TemplateService::render('page_complete_title', [], 'Müraciətiniz qəbul edildi') }}</h1>

    <p class="reveal reveal-2">{{ \App\Services\TemplateService::render('page_complete_body', [], 'Video müraciətiniz uğurla göndərildi. Tezliklə sizinlə əlaqə saxlanılacaq.') }}</p>

    <div class="perforation reveal reveal-3" aria-hidden="true"></div>

    <p class="named reveal reveal-3">{{ \App\Services\TemplateService::render('page_complete_greeting', [
        'ad'       => $application->name,
        'soyad'    => $application->surname,
        'ad_soyad' => $application->name . ' ' . $application->surname,
    ], 'Hörmətli ' . $application->name . ' ' . $application->surname . ', müraciətiniz üçün təşəkkür edirik.') }}</p>
</div>
@endsection
