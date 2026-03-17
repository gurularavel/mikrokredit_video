@extends('layouts.public')

@section('title', 'Video Kredit Müraciəti')

@section('content')
<div class="form-card">
    <div class="form-header">
        <h1>Kredit Müraciəti</h1>
        <p>Məlumatlarınızı daxil edin, telefon nömrənizə video müraciət linki göndəriləcək.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('applications.store') }}" class="application-form">
        @csrf
        <div class="form-group">
            <label for="name">Ad</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Adınız" required>
        </div>
        <div class="form-group">
            <label for="surname">Soyad</label>
            <input type="text" id="surname" name="surname" value="{{ old('surname') }}" placeholder="Soyadınız" required>
        </div>
        <div class="form-group">
            <label for="phone">Telefon nömrəsi</label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+994501234567" maxlength="13" required autocomplete="tel">
        </div>
        <button type="submit" class="btn btn-primary btn-block">SMS Göndər</button>
    </form>
</div>

@push('scripts')
<script>
(function () {
    var PREFIX = '+994';
    var input = document.getElementById('phone');
    if (!input) return;

    function enforce(input) {
        var val = input.value;
        if (!val.startsWith(PREFIX)) {
            var digits = val.replace(/\D/g, '');
            if (digits.startsWith('994')) digits = digits.slice(3);
            val = PREFIX + digits;
        }
        var suffix = val.slice(PREFIX.length).replace(/\D/g, '').slice(0, 9);
        input.value = PREFIX + suffix;
    }

    input.addEventListener('focus', function () {
        if (!this.value.startsWith(PREFIX)) this.value = PREFIX;
        var len = this.value.length;
        this.setSelectionRange(len, len);
    });

    input.addEventListener('input', function () { enforce(this); });

    input.addEventListener('keydown', function (e) {
        var start = this.selectionStart;
        if (start <= PREFIX.length && (e.key === 'Backspace' || e.key === 'Delete')) {
            e.preventDefault();
        }
    });

    // set initial value if pre-filled
    if (input.value) enforce(input);
})();
</script>
@endpush
@endsection
