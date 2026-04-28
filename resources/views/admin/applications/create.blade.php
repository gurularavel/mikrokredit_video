@extends('layouts.admin')

@section('title', 'Yeni Müraciət')

@section('content')
<div class="page-header">
    <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary btn-sm">← Geri</a>
    <h2>Yeni Müraciət</h2>
</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="detail-card" style="max-width:520px">

    @if($errors->any())
    <div class="alert alert-error" style="margin-bottom:18px">
        <ul style="margin:0;padding-left:18px">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.applications.store') }}">
        @csrf

        <div class="form-group">
            <label for="name">Ad <span class="req">*</span></label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Müştərinin adı" required maxlength="100">
        </div>

        <div class="form-group">
            <label for="surname">Soyad <span class="req">*</span></label>
            <input type="text" id="surname" name="surname" value="{{ old('surname') }}" placeholder="Müştərinin soyadı" required maxlength="100">
        </div>

        <div class="form-group">
            <label for="phone">Telefon nömrəsi <span class="req">*</span></label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone', '+994') }}" placeholder="+994501234567" maxlength="13" required autocomplete="off">
        </div>

        <div class="form-group">
            <label for="amount">Kredit məbləği (AZN)</label>
            <input type="number" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" min="0" step="0.01">
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;margin-top:4px">
            SMS Göndər
        </button>
    </form>
</div>

<style>
.req { color: var(--color-danger, #ef4444); }
</style>

@push('scripts')
<script>
(function () {
    var PREFIX = '+994';
    var input = document.getElementById('phone');
    if (!input) return;

    function enforce() {
        var val = input.value;
        if (!val.startsWith(PREFIX)) {
            var digits = val.replace(/\D/g, '');
            if (digits.startsWith('994')) digits = digits.slice(3);
            val = PREFIX + digits;
        }
        input.value = PREFIX + val.slice(PREFIX.length).replace(/\D/g, '').slice(0, 9);
    }

    input.addEventListener('focus', function () {
        if (!this.value.startsWith(PREFIX)) this.value = PREFIX;
        var len = this.value.length;
        this.setSelectionRange(len, len);
    });
    input.addEventListener('input', enforce);
    input.addEventListener('keydown', function (e) {
        if (this.selectionStart <= PREFIX.length && (e.key === 'Backspace' || e.key === 'Delete')) {
            e.preventDefault();
        }
    });
    enforce();
})();
</script>
@endpush
@endsection
