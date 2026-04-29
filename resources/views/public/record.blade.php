@extends('layouts.public')

@section('title', 'Video Müraciət')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="record-page" id="record-app"
     data-duration="{{ $duration }}"
     data-upload-url="{{ route('record.upload', $application->token) }}">

    @if($application->amount)
    <div class="amount-badge">
        <span class="amount-label">Kredit məbləği:</span>
        <span class="amount-value">{{ number_format($application->amount, 2, '.', ' ') }} AZN</span>
    </div>
    @endif

    @php
        $warningText = \App\Services\TemplateService::render('page_record_warning', [], 'Video çəkilişi zamanı yanınızda kimsənin olmadığından əmin olun.');
    @endphp
    @if($warningText)
    <div class="record-warning">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <span>{{ $warningText }}</span>
    </div>
    @endif

    <!-- Camera area — always visible -->
    <div class="camera-container" id="camera-container">
        <video id="live-video" autoplay muted playsinline style="display:none"></video>

        <div class="timer-overlay" id="timer-overlay">
            <svg class="timer-ring" viewBox="0 0 120 120">
                <circle class="timer-ring-bg" cx="60" cy="60" r="54"/>
                <circle class="timer-ring-progress" id="timer-ring-progress" cx="60" cy="60" r="54"/>
            </svg>
            <span class="timer-number" id="timer-number">{{ $duration }}</span>
        </div>

        <!-- Script overlay -->
        <div class="script-overlay" id="teleprompter">
            <div class="teleprompter-inner" id="teleprompter-inner">
                <p>{!! nl2br(e(\App\Services\TemplateService::render('page_record_script', [
                    'ad'       => $application->name,
                    'soyad'    => $application->surname,
                    'ad_soyad' => $application->name . ' ' . $application->surname,
                    'telefon'  => $application->phone,
                    'mebleg'   => $application->amount ? number_format($application->amount, 2, '.', ' ') . ' AZN' : '',
                ], 'Mən, ' . $application->name . ' ' . $application->surname . ', ' . ($application->amount ? number_format($application->amount, 2, '.', ' ') . ' AZN məbləğində ' : '') . 'kredit müraciəti etdiyimi təsdiq edirəm. Telefon nömrəm: ' . $application->phone . '. Bu müraciəti şüurlu şəkildə edirəm.'))) !!}</p>
            </div>
        </div>

        <!-- Early action buttons — overlay, visible at 10s remaining -->
        <div class="video-actions-overlay" id="early-actions" style="display:none">
            <button class="btn btn-secondary" id="early-rerecord-btn">Yenidən çək</button>
            <button class="btn btn-primary" id="early-confirm-btn">Göndər</button>
        </div>

        <!-- Start overlay -->
        <div id="start-screen" class="start-overlay">
            <button id="start-btn" class="btn btn-primary btn-start">&#9654; Başla</button>
        </div>
    </div>

    <!-- Preview after recording -->
    <div class="preview-container" id="preview-container" style="display:none">
        <video id="preview-video" controls playsinline></video>
        <!-- Preview actions overlay -->
        <div class="video-actions-overlay">
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
/* Full-height flex chain — only record page has camera-container/record-page */
html, body { height: 100%; }
.public-layout { display: flex; flex-direction: column; }
.public-layout .container {
    padding: 10px 12px;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}
.record-page {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}
#camera-container {
    flex: 1;
    min-height: 0;
    aspect-ratio: unset;
    max-height: none;
}
#preview-container {
    flex: 1;
    min-height: 0;
}

.amount-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    background: var(--color-card, #fff);
    border: 1.5px solid var(--color-border, #e2e8f0);
    border-radius: 8px;
    padding: 6px 12px;
    margin-bottom: 6px;
    font-size: .9rem;
}
.amount-label { color: var(--color-text-secondary, #64748b); font-weight: 500; }
.amount-value { font-weight: 700; color: var(--color-primary, #2563eb); }

.record-warning {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    background: #fefce8;
    border: 1.5px solid #fde047;
    border-radius: 8px;
    padding: 6px 12px;
    margin-bottom: 6px;
    color: #854d0e;
    font-size: .85rem;
    line-height: 1.45;
}
.record-warning svg {
    flex-shrink: 0;
    width: 17px; height: 17px;
    margin-top: 1px;
    stroke: #ca8a04;
}

/* Script overlay */
.script-overlay {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    background: rgba(0, 0, 0, 0.62);
    z-index: 5;
    padding: 14px 16px 20px;
}
.teleprompter-inner {
    font-size: 1.05rem;
    line-height: 1.7;
    color: #fff;
    font-weight: 600;
    text-shadow: 0 1px 4px rgba(0, 0, 0, 0.8);
}

/* Shared overlay for action buttons (early + preview) */
.video-actions-overlay {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    display: flex;
    gap: 10px;
    justify-content: center;
    padding: 16px 16px 22px;
    background: rgba(0, 0, 0, 0.72);
    z-index: 20;
    animation: fadeInUp .35s ease;
}

/* Start button overlay */
.start-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/recorder.js') }}?v={{ filemtime(public_path('js/recorder.js')) }}"></script>
@endpush
