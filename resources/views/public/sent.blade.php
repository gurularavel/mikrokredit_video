@extends('layouts.public')

@section('title', 'SMS Göndərildi')
@section('masthead_note', 'Addım 02 — SMS')

@section('content')
<div class="receipt">
    <div class="stamp stamp-success reveal reveal-1" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.18 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>
        </svg>
    </div>

    <h1 class="reveal reveal-2">{{ \App\Services\TemplateService::render('page_sent_title', [], 'SMS göndərildi') }}</h1>

    <p class="reveal reveal-2">{{ \App\Services\TemplateService::render('page_sent_body', [], 'Telefon nömrənizə video qeydiyyat linki göndərildi. Zəhmət olmasa telefonunuzu yoxlayın.') }}</p>

    <div class="reveal reveal-3">
        <span class="chip">
            <span class="dot"></span>
            {{ \App\Services\TemplateService::render('page_sent_hint', ['muddet' => config('sms.expiry_minutes')], 'Link ' . (int) config('sms.expiry_minutes') . ' dəqiqə ərzində etibarlıdır.') }}
        </span>
    </div>

    <div class="perforation reveal reveal-3" aria-hidden="true"></div>

    <div class="receipt-actions reveal reveal-4">
        <a href="{{ url('/') }}" class="btn btn-secondary">Geri qayıt</a>
    </div>
</div>
@endsection
