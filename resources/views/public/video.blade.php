@extends('layouts.public')

@section('title', \App\Services\SettingService::get('og_title', 'Video Müraciət'))

@push('head')
@php
    $ogTitle = \App\Services\SettingService::get('og_title', 'Video Müraciət');
    $ogDesc  = \App\Services\SettingService::get('og_description', 'Kredit müraciətinin video təsdiqi');
    $ogRaw   = \App\Services\SettingService::get('og_image_url');
    $ogImage = $ogRaw ? (str_starts_with($ogRaw, 'http') ? $ogRaw : url($ogRaw)) : null;
    $ogUrl   = request()->url();
@endphp
<meta property="og:type"        content="website">
<meta property="og:url"         content="{{ $ogUrl }}">
<meta property="og:title"       content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDesc }}">
@if($ogImage)
<meta property="og:image"       content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height"content="630">
@endif
<meta name="twitter:card"        content="summary_large_image">
<meta name="twitter:title"       content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDesc }}">
@if($ogImage)
<meta name="twitter:image"       content="{{ $ogImage }}">
@endif
@endpush

@section('content')
<div class="video-show-page">
    <div class="video-show-header">
        <h1>Video Müraciət</h1>
        <p>{{ $application->name }} {{ $application->surname }}</p>
    </div>

    <video controls playsinline class="video-show-player">
        <source src="{{ $application->videoUrl() }}" type="video/webm">
        <source src="{{ $application->videoUrl() }}" type="video/mp4">
    </video>

    @if($application->amount)
    <div class="video-show-meta">
        <span>Kredit məbləği: <strong>{{ number_format($application->amount, 2, '.', ' ') }} AZN</strong></span>
    </div>
    @endif
</div>
@endsection

@push('head')
<style>
.video-show-page {
    max-width: 680px;
    margin: 0 auto;
}
.video-show-header {
    text-align: center;
    margin-bottom: 20px;
}
.video-show-header h1 {
    font-size: 1.4rem;
    font-weight: 700;
}
.video-show-header p {
    color: var(--color-text-secondary, #64748b);
    margin-top: 4px;
}
.video-show-player {
    width: 100%;
    border-radius: 12px;
    background: #0B1629;
    box-shadow: 0 8px 32px rgba(11,22,41,.2);
    display: block;
}
.video-show-meta {
    margin-top: 14px;
    text-align: center;
    color: var(--color-text-secondary, #64748b);
    font-size: .9375rem;
}
.video-show-meta strong {
    color: var(--color-primary, #2563eb);
}
</style>
@endpush
