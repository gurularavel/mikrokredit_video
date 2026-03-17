@extends('layouts.admin')

@section('title', 'Müraciət #' . $application->id)

@section('content')
<div class="page-header">
    <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary btn-sm">← Geri</a>
    <h2>Müraciət #{{ $application->id }}</h2>
</div>

<div class="detail-grid">
    <div class="detail-card">
        <h3>Şəxsi Məlumatlar</h3>
        <dl>
            <dt>Ad</dt><dd>{{ $application->name }}</dd>
            <dt>Soyad</dt><dd>{{ $application->surname }}</dd>
            <dt>Telefon</dt><dd>{{ $application->phone }}</dd>
            <dt>Müraciət tarixi</dt><dd>{{ $application->created_at->format('d.m.Y H:i') }}</dd>
            <dt>SMS göndərildi</dt><dd>{{ $application->sms_sent_at ? $application->sms_sent_at->format('d.m.Y H:i') : '—' }}</dd>
        </dl>
    </div>

    <div class="detail-card">
        <h3>Status</h3>
        <p class="current-status">
            <span class="badge badge-{{ $application->status }}">
                @if($application->status === 'pending') Gözləyir
                @elseif($application->status === 'recorded') Qeyd edilib
                @else Baxılıb
                @endif
            </span>
        </p>

        <form method="POST" action="{{ route('admin.applications.updateStatus', $application) }}" class="status-form">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label>Statusu dəyiş</label>
                <select name="status">
                    <option value="pending" {{ $application->status === 'pending' ? 'selected' : '' }}>Gözləyir</option>
                    <option value="recorded" {{ $application->status === 'recorded' ? 'selected' : '' }}>Qeyd edilib</option>
                    <option value="reviewed" {{ $application->status === 'reviewed' ? 'selected' : '' }}>Baxılıb</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Yadda saxla</button>
        </form>
    </div>
</div>

@if($application->video_path)
<div class="video-card">
    <h3>Video</h3>
    <video controls class="video-player" preload="metadata">
        <source src="{{ $application->videoUrl() }}" type="video/webm">
        <source src="{{ $application->videoUrl() }}" type="video/mp4">
        Brauzeriniz video oynatmağı dəstəkləmir.
    </video>
    <div class="video-meta">
        <span>Qeyd tarixi: {{ $application->video_recorded_at ? $application->video_recorded_at->format('d.m.Y H:i') : '—' }}</span>
        @if($application->video_size)
            <span>Ölçü: {{ round($application->video_size / 1024 / 1024, 2) }} MB</span>
        @endif
    </div>
</div>
@else
<div class="detail-card">
    <p class="text-muted">Video hələ yüklənməyib.</p>
</div>
@endif
@endsection
