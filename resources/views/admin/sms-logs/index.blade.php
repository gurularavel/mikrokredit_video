@extends('layouts.admin')

@section('title', 'SMS Logları')

@section('content')
<div class="page-header">
    <h2><span class="eyebrow">Əməliyyat</span>SMS Logları</h2>
</div>

<div class="filter-form-card">
    <form method="GET" action="{{ route('admin.sms-logs.index') }}">
        <div class="filter-row">
            <div class="form-group">
                <label for="filter_phone">Telefon</label>
                <input type="text" id="filter_phone" name="phone" value="{{ request('phone') }}" placeholder="+994..." maxlength="13" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="sms_date_from">Başlanğıc tarix</label>
                <input type="text" name="date_from" id="sms_date_from" value="{{ request('date_from') }}" placeholder="gg.aa.iiii" autocomplete="off" readonly>
            </div>
            <div class="form-group">
                <label for="sms_date_to">Son tarix</label>
                <input type="text" name="date_to" id="sms_date_to" value="{{ request('date_to') }}" placeholder="gg.aa.iiii" autocomplete="off" readonly>
            </div>
            <div class="form-group filter-btns">
                <button type="submit" class="btn btn-primary">Axtar</button>
                <a href="{{ route('admin.sms-logs.index') }}" class="btn btn-secondary">Sıfırla</a>
            </div>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ad Soyad</th>
                    <th>Telefon</th>
                    <th>Link</th>
                    <th>SMS göndərildi</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="id-cell">{{ $log->id }}</td>
                    <td class="name-cell">{{ $log->name }} {{ $log->surname }}</td>
                    <td class="mono">{{ $log->phone }}</td>
                    <td>
                        @php $recordUrl = url('/record/' . $log->token) @endphp
                        <a href="{{ $recordUrl }}" target="_blank" rel="noopener" class="log-link" title="{{ $recordUrl }}">
                            {{ Str::limit($log->token, 16) }}…
                        </a>
                    </td>
                    <td class="num">{{ $log->sms_sent_at->format('d.m.Y H:i:s') }}</td>
                    <td>
                        <span class="badge badge-{{ $log->status }}">
                            @if($log->status === 'pending') Gözləyir
                            @elseif($log->status === 'recorded') Qeyd edilib
                            @else Baxılıb
                            @endif
                        </span>
                    </td>
                    <td class="actions">
                        <a href="{{ route('admin.applications.show', $log) }}" class="btn btn-secondary btn-sm">Bax</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                        <span>SMS tapılmadı</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="pagination-wrapper">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
flatpickr.localize(flatpickr.l10ns.az);
var fpCfg = { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd.m.Y', allowInput: false };
flatpickr('#sms_date_from', fpCfg);
flatpickr('#sms_date_to',   fpCfg);

(function () {
    var PREFIX = '+994';
    var input = document.getElementById('filter_phone');
    if (!input) return;
    input.addEventListener('focus', function () {
        if (!this.value.startsWith(PREFIX)) this.value = PREFIX;
        var len = this.value.length;
        this.setSelectionRange(len, len);
    });
    input.addEventListener('input', function () {
        var val = this.value;
        if (!val.startsWith(PREFIX)) {
            val = PREFIX + val.replace(/\D/g, '').replace(/^994/, '');
        }
        this.value = PREFIX + val.slice(PREFIX.length).replace(/\D/g, '').slice(0, 9);
    });
    input.addEventListener('keydown', function (e) {
        if (this.selectionStart <= PREFIX.length && (e.key === 'Backspace' || e.key === 'Delete')) {
            e.preventDefault();
        }
    });
})();
</script>
@endpush
@endsection
