@extends('layouts.public')

@section('title', 'Video Müraciət')
@section('body_class', 'is-record')
@section('masthead_note', 'Qeydiyyat aktiv')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="record-page" id="record-app"
     data-duration="{{ $duration }}"
     data-upload-url="{{ route('record.upload', $application->token) }}">

    <div class="record-strip">
        <span class="eyebrow">Video təsdiq</span>
        @if($application->amount)
        <span class="amount-badge">
            <span class="amount-label">Məbləğ</span>
            <span class="amount-value">{{ $application->formattedAmount() }}</span>
        </span>
        @endif
    </div>

    @php
        $warningText = \App\Services\TemplateService::render('page_record_warning', [], 'Video çəkilişi zamanı yanınızda kimsənin olmadığından əmin olun.');

        $vars       = $application->templateVars();
        $scriptText = \App\Services\TemplateService::renderFirst(
            $application->recordScriptKeys(),
            $vars,
            'Mən, ' . $vars['ad_soyad'] . ', ' . ($vars['mebleg'] ? $vars['mebleg'] . ' məbləğində ' : '')
                . 'kredit müraciəti etdiyimi təsdiq edirəm. Telefon nömrəm: ' . $vars['telefon']
                . '. Bu müraciəti şüurlu şəkildə edirəm.'
        );
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

        <!-- Teleprompter -->
        <div class="script-overlay" id="teleprompter">
            <div class="teleprompter-inner" id="teleprompter-inner">
                <p>{!! nl2br(e($scriptText)) !!}</p>
            </div>
        </div>

        <!-- Early action buttons — overlay, visible at 10s remaining -->
        <div class="video-actions-overlay" id="early-actions" style="display:none">
            <button class="btn btn-ghost-light" id="early-rerecord-btn">Yenidən çək</button>
            <button class="btn btn-copper" id="early-confirm-btn">Göndər</button>
        </div>

        <!-- Start overlay -->
        <div id="start-screen" class="start-overlay">
            <div class="start-overlay-inner">
                <span class="start-hint">Hazır olduqda başlayın</span>
                <button id="start-btn" class="btn btn-start">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor" aria-hidden="true"><path d="M7 4.5v15l13-7.5z"/></svg>
                    Başla
                </button>
                <span class="start-hint" style="letter-spacing:.12em;opacity:.72">{{ $duration }} saniyə · bir dəfəlik</span>
            </div>
        </div>
    </div>

    <!-- Preview after recording -->
    <div class="preview-container" id="preview-container" style="display:none">
        <video id="preview-video" controls playsinline></video>
        <div class="video-actions-overlay">
            <button class="btn btn-ghost-light" id="rerecord-btn">Yenidən çək</button>
            <button class="btn btn-copper" id="confirm-btn">Göndər</button>
        </div>
    </div>

    <!-- Upload progress -->
    <div class="upload-overlay" id="upload-overlay" style="display:none">
        <div class="spinner"></div>
        <p>Video yüklənir</p>
    </div>

    <div class="status-msg" id="status-msg"></div>

    @php $recordBottomNote = \App\Services\SettingService::get('record_bottom_note'); @endphp
    @if($recordBottomNote)
    <div class="record-admin-notes">
        {!! $recordBottomNote !!}
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/recorder.js') }}?v={{ filemtime(public_path('js/recorder.js')) }}"></script>
@endpush
