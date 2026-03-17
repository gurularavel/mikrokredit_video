@extends('layouts.public')

@section('title', 'SMS Göndərildi')

@section('content')
<div class="status-card">
    <div class="status-icon success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.67A2 2 0 012 .84h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
        </svg>
    </div>
    <h2>{{ \App\Services\TemplateService::render('page_sent_title', [], 'SMS Göndərildi!') }}</h2>
    <p>{{ \App\Services\TemplateService::render('page_sent_body', [], 'Telefon nömrənizə video qeydiyyat linki göndərildi. Zəhmət olmasa telefonunuzu yoxlayın.') }}</p>
    <p class="hint">{{ \App\Services\TemplateService::render('page_sent_hint', ['muddet' => config('sms.expiry_minutes')], 'Link ' . config('sms.expiry_minutes') . ' dəqiqə ərzində etibarlıdır.') }}</p>
    <a href="{{ route('applications.index') }}" class="btn btn-secondary">Geri qayıt</a>
</div>
@endsection
