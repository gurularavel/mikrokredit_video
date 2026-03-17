@extends('layouts.admin')

@section('title', 'Müraciətlər')

@section('content')
<div class="page-header">
    <h2>Müraciətlər</h2>
</div>

<div class="filter-form-card">
    <form method="GET" action="{{ route('admin.applications.index') }}" class="filter-form">
        <div class="form-row">
            <div class="form-group">
                <label>Telefon</label>
                <input type="text" name="phone" value="{{ request('phone') }}" placeholder="+994...">
            </div>
            <div class="form-group">
                <label>Başlanğıc tarix</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="form-group">
                <label>Son tarix</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="">Hamısı</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Gözləyir</option>
                    <option value="recorded" {{ request('status') === 'recorded' ? 'selected' : '' }}>Qeyd edilib</option>
                    <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Baxılıb</option>
                </select>
            </div>
            <div class="form-group form-actions">
                <button type="submit" class="btn btn-primary">Axtar</button>
                <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary">Sıfırla</a>
            </div>
        </div>
    </form>
</div>

<div class="table-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
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
                <td>{{ $app->id }}</td>
                <td>{{ $app->name }} {{ $app->surname }}</td>
                <td>{{ $app->phone }}</td>
                <td>
                    <span class="badge badge-{{ $app->status }}">
                        @if($app->status === 'pending') Gözləyir
                        @elseif($app->status === 'recorded') Qeyd edilib
                        @else Baxılıb
                        @endif
                    </span>
                </td>
                <td>{{ $app->created_at->format('d.m.Y H:i') }}</td>
                <td>
                    @if($app->video_path)
                        <span class="badge badge-recorded">Var</span>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.applications.show', $app) }}" class="btn btn-sm btn-secondary">Bax</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center text-muted">Müraciət tapılmadı.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination-wrapper">
        {{ $applications->links() }}
    </div>
</div>
@endsection
