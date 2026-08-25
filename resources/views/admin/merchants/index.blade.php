@extends('layouts.admin')

@section('title', 'Merchantlər')

@section('content')
<div class="page-header">
    <h2><span class="eyebrow">Əməliyyat</span>Merchantlər</h2>
    <a href="{{ route('admin.merchants.create') }}" class="btn btn-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Yeni merchant
    </a>
</div>

<div class="table-card">
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
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
                    <td class="id-cell">{{ $merchant->id }}</td>
                    <td>
                        @if($merchant->logo)
                            <img class="logo-thumb" src="{{ asset('storage/' . $merchant->logo) }}" alt="{{ $merchant->name }}">
                        @else
                            <span class="logo-thumb logo-thumb--empty" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1.5-5h15L21 9"/><path d="M4 9v11h16V9"/><path d="M10 20v-5h4v5"/></svg>
                            </span>
                        @endif
                    </td>
                    <td class="name-cell">{{ $merchant->name }}</td>
                    <td><code class="code-pill">{{ $merchant->login }}</code></td>
                    <td>
                        <div class="key-field">
                            <input type="password" id="key_{{ $merchant->id }}" value="{{ $merchant->auth_key }}" readonly>
                            <button type="button" class="key-icon-btn" data-pw-toggle="key_{{ $merchant->id }}" title="Göstər / gizlət" aria-label="Göstər / gizlət"></button>
                            <button type="button" class="key-icon-btn" data-copy="{{ $merchant->auth_key }}" title="Kopyala" aria-label="Kopyala">
                                <svg class="icon-copy" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                                <svg class="icon-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:none;color:var(--moss)"><polyline points="20 6 9 17 4 12"/></svg>
                            </button>
                        </div>
                    </td>
                    <td class="num">{{ $merchant->applications()->count() }}</td>
                    <td class="actions">
                        <a href="{{ route('admin.merchants.edit', $merchant) }}" class="btn btn-secondary btn-sm">Redaktə</a>
                        <form method="POST" action="{{ route('admin.merchants.destroy', $merchant) }}"
                              onsubmit="return confirm('«{{ $merchant->name }}» merchantını silmək istədiyinizə əminsiniz?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Sil</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1.5-5h15L21 9"/><path d="M4 9v11h16V9"/><path d="M10 20v-5h4v5"/></svg>
                        <span>Merchant tapılmadı</span>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($merchants->hasPages())
    <div class="pagination-wrapper">
        {{ $merchants->links() }}
    </div>
    @endif
</div>
@endsection
