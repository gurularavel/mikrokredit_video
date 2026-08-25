@extends('layouts.admin')

@section('title', 'Müraciət #' . $application->id)

@php
    $copyIcon = '<svg class="icon-copy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>'
              . '<svg class="icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:none;color:var(--moss)"><polyline points="20 6 9 17 4 12"/></svg>';
@endphp

@section('content')
<div class="page-header">
    <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="11 18 5 12 11 6"/></svg>
        Geri
    </a>
    <h2><span class="eyebrow">Müraciət № {{ $application->id }}</span>{{ $application->name }} {{ $application->surname }}</h2>
    <span class="badge badge-{{ $application->status }}">
        @if($application->status === 'pending') Gözləyir
        @elseif($application->status === 'recorded') Qeyd edilib
        @else Baxılıb
        @endif
    </span>
</div>

<div class="detail-grid">
    <div class="detail-card">
        <h3>Şəxsi məlumatlar</h3>
        <dl class="detail-list">
            <dt>Ad</dt><dd>{{ $application->name }}</dd>
            <dt>Soyad</dt><dd>{{ $application->surname }}</dd>
            <dt>Telefon</dt><dd class="num">{{ $application->phone }}</dd>
            @if($application->amount)
            <dt>Məbləğ</dt><dd class="money">{{ number_format($application->amount, 2, '.', ' ') }} AZN</dd>
            @endif
            @if($application->app_id)
            <dt>APP ID</dt>
            <dd>
                <div class="sms-link-row">
                    <span class="sms-link-text">{{ $application->app_id }}</span>
                    <button type="button" class="key-icon-btn" data-copy="{{ $application->app_id }}" title="Kopyala" aria-label="Kopyala">{!! $copyIcon !!}</button>
                </div>
            </dd>
            @endif
            <dt>Video mətni</dt><dd>{{ \App\Models\Application::M_TYPES[$application->m_type] ?? ('Tip ' . $application->m_type) }}</dd>
            <dt>Müraciət tarixi</dt><dd class="num">{{ $application->created_at->format('d.m.Y H:i') }}</dd>
            <dt>SMS göndərildi</dt><dd class="num">{{ $application->sms_sent_at ? $application->sms_sent_at->format('d.m.Y H:i') : '—' }}</dd>
            <dt>Link açıldı</dt><dd class="num">{{ $application->link_opened_at ? $application->link_opened_at->format('d.m.Y H:i:s') : '—' }}</dd>
            <dt>SMS linki</dt>
            <dd>
                <div class="sms-link-row">
                    <span class="sms-link-text">{{ route('record.show', $application->token) }}</span>
                    <button type="button" class="key-icon-btn" data-copy="{{ route('record.show', $application->token) }}" title="Kopyala" aria-label="Kopyala">{!! $copyIcon !!}</button>
                </div>
            </dd>
            @if($application->app_id)
            <dt>Video linki</dt>
            <dd>
                <div class="sms-link-row">
                    <span class="sms-link-text">{{ route('video.show', $application->app_id) }}</span>
                    <button type="button" class="key-icon-btn" data-copy="{{ route('video.show', $application->app_id) }}" title="Kopyala" aria-label="Kopyala">{!! $copyIcon !!}</button>
                </div>
            </dd>
            @endif
        </dl>
    </div>

    <div class="detail-card">
        <h3>Status</h3>

        <form method="POST" action="{{ route('admin.applications.updateStatus', $application) }}" class="status-form">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="status">Statusu dəyiş</label>
                <select name="status" id="status">
                    <option value="pending"  {{ $application->status === 'pending'  ? 'selected' : '' }}>Gözləyir</option>
                    <option value="recorded" {{ $application->status === 'recorded' ? 'selected' : '' }}>Qeyd edilib</option>
                    <option value="reviewed" {{ $application->status === 'reviewed' ? 'selected' : '' }}>Baxılıb</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Yadda saxla</button>
        </form>
    </div>
</div>

@if($application->video_path)
<div class="video-card">
    <h3>Video qeyd</h3>
    <video controls class="video-player" preload="metadata">
        <source src="{{ $application->videoUrl() }}" type="video/webm">
        <source src="{{ $application->videoUrl() }}" type="video/mp4">
        Brauzeriniz video oynatmağı dəstəkləmir.
    </video>
    <div class="video-meta">
        <span>Qeyd tarixi <strong>{{ $application->video_recorded_at ? $application->video_recorded_at->format('d.m.Y H:i') : '—' }}</strong></span>
        @if($application->video_size)
            <span>Ölçü <strong>{{ round($application->video_size / 1024 / 1024, 2) }} MB</strong></span>
        @endif
    </div>
</div>
@else
<div class="detail-card">
    <div class="video-empty">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="14" height="12" rx="2.5"/><path d="M16 10.5l6-3.5v10l-6-3.5z"/><line x1="3" y1="21" x2="21" y2="3"/></svg>
        <span>Video hələ yüklənməyib.</span>
    </div>
</div>
@endif
@endsection
