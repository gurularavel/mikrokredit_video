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

    <div class="script-box">
        <p>{!! nl2br(e(\App\Services\TemplateService::render('page_record_script', [
            'ad'       => $application->name,
            'soyad'    => $application->surname,
            'ad_soyad' => $application->name . ' ' . $application->surname,
            'telefon'  => $application->phone,
        ], 'Mən, ' . $application->name . ' ' . $application->surname . ', bu video ilə kredit müraciəti etdiyimi təsdiq edirəm. Telefon nömrəm: ' . $application->phone . '. Bu müraciəti şüurlu şəkildə edirəm.'))) !!}</p>
    </div>

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

@push('scripts')
<script src="{{ asset('js/recorder.js') }}"></script>
@endpush
