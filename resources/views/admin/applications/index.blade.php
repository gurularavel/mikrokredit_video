@extends('layouts.admin')

@section('title', 'Müraciətlər')

@section('content')
<div class="page-header">
    <h2><span class="eyebrow">Əməliyyat</span>Müraciətlər</h2>
    <a href="{{ route('admin.applications.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Yeni müraciət
    </a>
</div>

<div class="filter-form-card">
    <form method="GET" action="{{ route('admin.applications.index') }}">
        <div class="filter-row">
            <div class="form-group">
                <label for="filter_phone">Telefon</label>
                <input type="text" id="filter_phone" name="phone" value="{{ request('phone') }}" placeholder="+994..." maxlength="13" autocomplete="off">
            </div>
            <div class="form-group">
                <label for="date_from">Başlanğıc tarix</label>
                <input type="text" name="date_from" id="date_from" value="{{ request('date_from') }}" placeholder="gg.aa.iiii" autocomplete="off" readonly>
            </div>
            <div class="form-group">
                <label for="date_to">Son tarix</label>
                <input type="text" name="date_to" id="date_to" value="{{ request('date_to') }}" placeholder="gg.aa.iiii" autocomplete="off" readonly>
            </div>
            <div class="form-group">
                <label for="filter_status">Status</label>
                <select name="status" id="filter_status">
                    <option value="">Hamısı</option>
                    <option value="pending"   {{ request('status') === 'pending'   ? 'selected' : '' }}>Gözləyir</option>
                    <option value="recorded"  {{ request('status') === 'recorded'  ? 'selected' : '' }}>Qeyd edilib</option>
                    <option value="reviewed"  {{ request('status') === 'reviewed'  ? 'selected' : '' }}>Baxılıb</option>
                </select>
            </div>
            <div class="form-group filter-btns">
                <button type="submit" class="btn btn-primary">Axtar</button>
                <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary">Sıfırla</a>
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
                    <th>Status</th>
                    <th>Müraciət tarixi</th>
                    <th>Video</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                <tr>
                    <td class="id-cell">{{ $app->id }}</td>
                    <td class="name-cell">{{ $app->name }} {{ $app->surname }}</td>
                    <td class="mono">{{ $app->phone }}</td>
                    <td>
                        <span class="badge badge-{{ $app->status }}">
                            @if($app->status === 'pending') Gözləyir
                            @elseif($app->status === 'recorded') Qeyd edilib
                            @else Baxılıb
                            @endif
                        </span>
                    </td>
                    <td class="num">{{ $app->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        @if($app->video_path)
                            <span class="badge badge-reviewed">Var</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="actions">
                        <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-sm btn-secondary">Bax</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <span>Müraciət tapılmadı</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($applications->hasPages())
    <div class="pagination-wrapper">
        {{ $applications->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
flatpickr.localize(flatpickr.l10ns.az);
var fpCfg = { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd.m.Y', allowInput: false };
flatpickr('#date_from', fpCfg);
flatpickr('#date_to',   fpCfg);

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
