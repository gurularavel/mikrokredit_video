@extends('layouts.admin')

@section('title', 'SMS Logları')

@section('content')
<div class="page-header">
    <h2>SMS Logları</h2>
</div>

<div class="filter-form-card">
    <form method="GET" action="{{ route('admin.sms-logs.index') }}">
        <div class="form-row">
            <div class="form-group">
                <label>Telefon</label>
                <input type="text" name="phone" value="{{ request('phone') }}" placeholder="+994...">
            </div>
            <div class="form-group">
                <label>Tarixdən</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="form-group">
                <label>Tarixə</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="form-group" style="display:flex;align-items:flex-end;gap:8px">
                <button type="submit" class="btn btn-primary btn-sm">Axtar</button>
                <a href="{{ route('admin.sms-logs.index') }}" class="btn btn-secondary btn-sm">Sıfırla</a>
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
                <td colspan="6" class="text-center text-muted" style="padding:32px">SMS tapılmadı</td>
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
