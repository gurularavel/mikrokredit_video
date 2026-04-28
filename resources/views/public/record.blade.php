@extends('layouts.public')

@section('title', 'Video Qeydiyyat')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="record-page" id="record-app"
     data-duration="{{ $duration }}"
     data-upload-url="{{ route('record.upload', $application->token) }}">

    <div class="record-header">
        <h1>Video Müraciət</h1>
        <p>Kamera avtomatik başlayacaq. Aşağıdakı mətni oxuyun:</p>
    </div>

    @if($application->amount)
    <div class="amount-badge">
        <span class="amount-label">Kredit məbləği:</span>
        <span class="amount-value">{{ number_format($application->amount, 2, '.', ' ') }} AZN</span>
    </div>
    @endif

    <div class="script-box">
        <p>{!! nl2br(e(\App\Services\TemplateService::render('page_record_script', [
            'ad'       => $application->name,
            'soyad'    => $application->surname,
            'ad_soyad' => $application->name . ' ' . $application->surname,
            'telefon'  => $application->phone,
            'mebleg'   => $application->amount ? number_format($application->amount, 2, '.', ' ') . ' AZN' : '',
        ], 'Mən, ' . $application->name . ' ' . $application->surname . ', ' . ($application->amount ? number_format($application->amount, 2, '.', ' ') . ' AZN məbləğində ' : '') . 'kredit müraciəti etdiyimi təsdiq edirəm. Telefon nömrəm: ' . $application->phone . '. Bu müraciəti şüurlu şəkildə edirəm.'))) !!}</p>
    </div>

    @php
        $warningText = \App\Services\TemplateService::render('page_record_warning', [], 'Video çəkilişi zamanı yanınızda kimsənin olmadığından əmin olun.');
    @endphp
    @if($warningText)
    <div class="record-warning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <span>{{ $warningText }}</span>
    </div>
    @endif

    <!-- Start button -->
    <div id="start-screen" class="start-screen">
        <button id="start-btn" class="btn btn-primary btn-start">&#9654; Başla</button>
    </div>

    <!-- Live camera view -->
    <div class="camera-container" id="camera-container" style="display:none">
        <video id="live-video" autoplay muted playsinline></video>
        <div class="timer-overlay" id="timer-overlay">
            <svg class="timer-ring" viewBox="0 0 120 120">
                <circle class="timer-ring-bg" cx="60" cy="60" r="54"/>
                <circle class="timer-ring-progress" id="timer-ring-progress" cx="60" cy="60" r="54"/>
            </svg>
            <span class="timer-number" id="timer-number">{{ $duration }}</span>
        </div>
    </div>

    <!-- Preview after recording -->
    <div class="preview-container" id="preview-container" style="display:none">
        <video id="preview-video" controls playsinline></video>
        <div class="preview-actions">
            <button class="btn btn-secondary" id="rerecord-btn">Yenidən çək</button>
            <button class="btn btn-primary" id="confirm-btn">Göndər</button>
        </div>
    </div>

    <!-- Upload progress -->
    <div class="upload-overlay" id="upload-overlay" style="display:none">
        <div class="spinner"></div>
        <p>Video yüklənir...</p>
    </div>

    <div class="status-msg" id="status-msg"></div>
</div>
@endsection

@push('head')
<style>
.amount-badge {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--color-card, #fff);
    border: 1.5px solid var(--color-border, #e2e8f0);
    border-radius: 10px;
    padding: 12px 18px;
    margin-bottom: 14px;
    font-size: 1rem;
}
.amount-label {
    color: var(--color-text-secondary, #64748b);
    font-weight: 500;
}
.amount-value {
    font-weight: 700;
    font-size: 1.15rem;
    color: var(--color-primary, #2563eb);
}
.record-warning {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    background: #fefce8;
    border: 1.5px solid #fde047;
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 16px;
    color: #854d0e;
    font-size: .9375rem;
    line-height: 1.5;
}
.record-warning svg {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    margin-top: 1px;
    stroke: #ca8a04;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/recorder.js') }}"></script>
@endpush
