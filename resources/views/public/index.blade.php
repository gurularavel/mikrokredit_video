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
            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+994501234567" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">SMS Göndər</button>
    </form>
</div>
@endsection
