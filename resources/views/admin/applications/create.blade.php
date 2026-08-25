@extends('layouts.admin')

@section('title', 'Yeni Müraciət')

@section('content')
<div class="page-header">
    <a href="{{ route('admin.applications.index') }}" class="btn btn-secondary btn-sm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="11 18 5 12 11 6"/></svg>
        Geri
    </a>
    <h2><span class="eyebrow">Müraciətlər</span>Yeni müraciət</h2>
</div>

<div class="detail-card form-narrow">
    <h3>Müştəri məlumatları</h3>

    @if($errors->any())
    <div class="alert alert-error">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.applications.store') }}">
        @csrf

        <div class="form-row">
            <div class="form-group" style="flex:1">
                <label for="name">Ad <span class="req">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Müştərinin adı" required maxlength="100" autofocus>
            </div>

            <div class="form-group" style="flex:1">
                <label for="surname">Soyad <span class="req">*</span></label>
                <input type="text" id="surname" name="surname" value="{{ old('surname') }}" placeholder="Müştərinin soyadı" required maxlength="100">
            </div>
        </div>

        <div class="form-group">
            <label for="phone">Telefon nömrəsi <span class="req">*</span></label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone', '+994') }}" placeholder="+994501234567" maxlength="13" required autocomplete="off" inputmode="numeric">
            <span class="field-hint">Təsdiq linki bu nömrəyə SMS ilə göndəriləcək.</span>
        </div>

        <div class="form-group">
            <label for="amount">Kredit məbləği (AZN)</label>
            <input type="number" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" min="0" step="0.01">
            <span class="field-hint">Boş buraxılsa, məbləğ video mətnində göstərilməyəcək.</span>
        </div>

        <div class="form-group">
            <label for="m_type">Video mətni</label>
            <select id="m_type" name="m_type">
                @foreach(\App\Models\Application::M_TYPES as $value => $label)
                    <option value="{{ $value }}" {{ (int) old('m_type', 1) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <span class="field-hint">Çəkiliş ekranında hansı şablonun oxunacağını təyin edir. Mətnlər «Mesaj Şablonları» bölməsindən redaktə olunur.</span>
        </div>

        <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            SMS göndər
        </button>
    </form>
</div>

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
