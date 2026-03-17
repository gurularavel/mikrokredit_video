@extends('layouts.admin')

@section('title', 'SMS Logları')

@section('content')
<div class="page-header">
    <h2>SMS Logları</h2>
</div>

<div class="filter-form-card">
    <form method="GET" action="{{ route('admin.sms-logs.index') }}">
        <div class="filter-row">
            <div class="form-group">
                <label>Telefon</label>
                <input type="text" id="filter_phone" name="phone" value="{{ request('phone') }}" placeholder="+994..." maxlength="13" autocomplete="off">
            </div>
            <div class="form-group">
                <label>Başlanğıc tarix</label>
                <input type="text" name="date_from" id="sms_date_from" value="{{ request('date_from') }}" placeholder="gg.aa.iiii" autocomplete="off" readonly>
            </div>
            <div class="form-group">
                <label>Son tarix</label>
                <input type="text" name="date_to" id="sms_date_to" value="{{ request('date_to') }}" placeholder="gg.aa.iiii" autocomplete="off" readonly>
            </div>
            <div class="form-group filter-btns">
                <button type="submit" class="btn btn-primary">Axtar</button>
                <a href="{{ route('admin.sms-logs.index') }}" class="btn btn-secondary">Sıfırla</a>
            </div>
        </div>
    </form>
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

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Ad Soyad</th>
                <th>Telefon</th>
                <th>Link</th>
                <th>SMS Göndərildi</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ $log->name }} {{ $log->surname }}</td>
                <td>{{ $log->phone }}</td>
                <td>
                    @php $recordUrl = url('/record/' . $log->token) @endphp
                    <a href="{{ $recordUrl }}" target="_blank" class="log-link" title="{{ $recordUrl }}">
                        {{ Str::limit($log->token, 16) }}…
                    </a>
                </td>
                <td>{{ $log->sms_sent_at->format('d.m.Y H:i:s') }}</td>
                <td>
                    <span class="badge badge-{{ $log->status }}">
                        @if($log->status === 'pending') Gözləyir
                        @elseif($log->status === 'recorded') Qeyd edilib
                        @else Baxılıb
                        @endif
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.applications.show', $log) }}" class="btn btn-secondary btn-sm">Bax</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted" style="padding:32px">SMS tapılmadı</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($logs->hasPages())
    <div class="pagination-wrapper">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
