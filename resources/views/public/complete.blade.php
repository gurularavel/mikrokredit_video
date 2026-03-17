@extends('layouts.public')

@section('title', 'Müraciət Tamamlandı')

@section('content')
<div class="status-card">
    <div class="status-icon success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
    </div>
    <h2>{{ \App\Services\TemplateService::render('page_complete_title', [], 'Müraciətiniz qəbul edildi!') }}</h2>
    <p>{{ \App\Services\TemplateService::render('page_complete_body', [], 'Video müraciətiniz uğurla göndərildi. Tezliklə sizinlə əlaqə saxlanılacaq.') }}</p>
    <p>{{ \App\Services\TemplateService::render('page_complete_greeting', [
        'ad'       => $application->name,
        'soyad'    => $application->surname,
        'ad_soyad' => $application->name . ' ' . $application->surname,
    ], 'Hörmətli ' . $application->name . ' ' . $application->surname . ', müraciətiniz üçün təşəkkür edirik.') }}</p>
</div>
@endsection
