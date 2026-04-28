@extends('layouts.admin')

@section('title', 'Merchantlər')

@section('content')
<div class="page-header" style="justify-content:space-between">
    <h2>Merchantlər</h2>
    <a href="{{ route('admin.merchants.create') }}" class="btn btn-primary">+ Yeni merchant</a>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Logo</th>
                <th>Ad</th>
                <th>Login</th>
                <th>Auth Key</th>
                <th>Müraciətlər</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($merchants as $merchant)
            <tr>
                <td>{{ $merchant->id }}</td>
                <td>
                    @if($merchant->logo)
                        <img src="{{ asset('storage/' . $merchant->logo) }}" alt="{{ $merchant->name }}"
                             style="width:40px;height:40px;object-fit:contain;border-radius:var(--radius-sm);border:1px solid var(--color-border)">
                    @else
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;background:var(--color-bg);border:1px solid var(--color-border);border-radius:var(--radius-sm);font-size:18px">&#127978;</span>
                    @endif
                </td>
                <td><strong>{{ $merchant->name }}</strong></td>
                <td><code style="font-size:.85rem;background:var(--color-bg);padding:2px 7px;border-radius:var(--radius-xs);border:1px solid var(--color-border)">{{ $merchant->login }}</code></td>
                <td>
                    <div style="display:flex;align-items:center;gap:6px;min-width:300px">
                        <input
                            type="password"
                            id="key_{{ $merchant->id }}"
                            value="{{ $merchant->auth_key }}"
                            readonly
                            style="flex:1;min-width:0;padding:7px 11px;border:1.5px solid var(--color-border);border-radius:var(--radius-md);background:var(--color-card);color:var(--color-text);font-family:'Courier New',monospace;font-size:.78rem;letter-spacing:.04em;transition:border-color .2s"
                        >
                        <button
                            type="button"
                            class="key-icon-btn key-eye"
                            data-target="key_{{ $merchant->id }}"
                            title="Göstər / gizlət"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                        <button
                            type="button"
                            class="key-icon-btn key-copy"
                            data-copy="{{ $merchant->auth_key }}"
                            title="Kopyala"
                        >
                            <svg class="icon-copy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            <svg class="icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:none;color:var(--color-success)"><polyline points="20 6 9 17 4 12"/></svg>
                        </button>
                    </div>
                </td>
                <td>{{ $merchant->applications()->count() }}</td>
                <td style="white-space:nowrap">
                    <a href="{{ route('admin.merchants.edit', $merchant) }}" class="btn btn-secondary btn-sm">Redaktə</a>
                    <form method="POST" action="{{ route('admin.merchants.destroy', $merchant) }}"
                          style="display:inline" onsubmit="return confirm('Silmək istədiyinizə əminsiniz?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm" style="background:var(--color-danger);color:#fff">Sil</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted" style="padding:32px">Merchant tapılmadı</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($merchants->hasPages())
    <div class="pagination-wrapper">
        {{ $merchants->links() }}
    </div>
    @endif
</div>

@push('scripts')
<style>
.key-icon-btn {
    flex-shrink: 0;
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1.5px solid var(--color-border);
    border-radius: var(--radius-md);
    background: var(--color-card);
    cursor: pointer;
    color: var(--color-text-muted);
    transition: color .15s, border-color .15s, background .15s;
    padding: 0;
}
.key-icon-btn:hover {
    color: var(--color-text-secondary);
    border-color: #CBD5E1;
    background: var(--color-bg);
}
.key-icon-btn svg {
    width: 15px;
    height: 15px;
    pointer-events: none;
}
</style>
<script>
document.querySelectorAll('.key-eye').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var input = document.getElementById(this.dataset.target);
        var show  = input.type === 'password';
        input.type = show ? 'text' : 'password';
        this.innerHTML = show
            ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    });
});

function copyText(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    }
    var el = document.createElement('textarea');
    el.value = text;
    el.style.cssText = 'position:fixed;left:-9999px;top:-9999px;opacity:0';
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
            }, 1600);
        });
    });
});
</script>
@endpush
@endsection
