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
            @if($application->amount)
            <dt>Məbləğ</dt><dd>{{ number_format($application->amount, 2) }} AZN</dd>
            @endif
            <dt>Müraciət tarixi</dt><dd>{{ $application->created_at->format('d.m.Y H:i') }}</dd>
            <dt>SMS göndərildi</dt><dd>{{ $application->sms_sent_at ? $application->sms_sent_at->format('d.m.Y H:i') : '—' }}</dd>
            <dt>Link açıldı</dt><dd>{{ $application->link_opened_at ? $application->link_opened_at->format('d.m.Y H:i:s') : '—' }}</dd>
            <dt>SMS linki</dt>
            <dd>
                <div class="sms-link-row">
                    <span class="sms-link-text">{{ route('record.show', $application->token) }}</span>
                    <button type="button" class="key-icon-btn key-copy" data-copy="{{ route('record.show', $application->token) }}" title="Kopyala">
                        <svg class="icon-copy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                        <svg class="icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:none;color:var(--color-success)"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                </div>
            </dd>
            @if($application->app_id)
            <dt>Video linki</dt>
            <dd>
                <div class="sms-link-row">
                    <span class="sms-link-text">{{ route('video.show', $application->app_id) }}</span>
                    <button type="button" class="key-icon-btn key-copy" data-copy="{{ route('video.show', $application->app_id) }}" title="Kopyala">
                        <svg class="icon-copy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                        <svg class="icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:none;color:var(--color-success)"><polyline points="20 6 9 17 4 12"/></svg>
                    </button>
                </div>
            </dd>
            @endif
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
@push('scripts')
<style>

.sms-link-row {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
}
.sms-link-text {
    font-family: 'Courier New', monospace;
    font-size: .78rem;
    color: var(--color-text-secondary);
    word-break: break-all;
    flex: 1;
}
.key-icon-btn {
    flex-shrink: 0;
    width: 34px; height: 34px;
    display: inline-flex; align-items: center; justify-content: center;
    border: 1.5px solid var(--color-border);
    border-radius: var(--radius-md);
    background: var(--color-card);
    color: var(--color-text);
    cursor: pointer;
    transition: color .15s, border-color .15s, background .15s;
    padding: 0;
}
.key-icon-btn:hover { color: var(--color-text-secondary); border-color: #CBD5E1; background: var(--color-bg); }
.key-icon-btn svg { width: 15px; height: 15px; pointer-events: none; }
</style>
<script>
function copyText(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    }
    var el = document.createElement('textarea');
    el.value = text;
    el.style.cssText = 'position:fixed;opacity:0';
    document.body.appendChild(el);
    el.focus();
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    return Promise.resolve();
}

document.querySelectorAll('.key-copy').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var self = this;
        copyText(this.dataset.copy).then(function () {
            var iconCopy  = self.querySelector('.icon-copy');
            var iconCheck = self.querySelector('.icon-check');
            iconCopy.style.display  = 'none';
            iconCheck.style.display = '';
            setTimeout(function () {
                iconCopy.style.display  = '';
                iconCheck.style.display = 'none';
            }, 2000);
        });
    });
});
</script>
@endpush

@endsection
