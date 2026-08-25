@extends('layouts.public')

@section('title', \App\Services\SettingService::get('og_title', 'Video Müraciət'))
@section('masthead_note', 'Arxiv qeydi')

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
<div class="reel">
    <header class="reel-head reveal reveal-1">
        <div>
            <span class="eyebrow">Video müraciət</span>
            <h1>{{ $application->name }} {{ $application->surname }}</h1>
        </div>
        <div class="who">
            @if($application->app_id)
            <div>ID {{ $application->app_id }}</div>
            @endif
            <div>{{ ($application->video_recorded_at ?? $application->created_at)->format('d.m.Y · H:i') }}</div>
        </div>
    </header>

    <div class="reel-frame reveal reveal-2">
        <video controls playsinline preload="metadata">
            <source src="{{ $application->publicStreamUrl() }}" type="video/webm">
            <source src="{{ $application->publicStreamUrl() }}" type="video/mp4">
            Brauzeriniz video oynatmağı dəstəkləmir.
        </video>
    </div>

    <div class="reel-meta reveal reveal-3">
        @if($application->amount)
        <span>Kredit məbləği <strong>{{ number_format($application->amount, 2, '.', ' ') }} AZN</strong></span>
        @endif
        <span>Telefon <strong>{{ $application->phone }}</strong></span>
    </div>
</div>
@endsection
