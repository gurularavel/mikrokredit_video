@extends('layouts.admin')

@section('title', 'Şifrə dəyiş')

@section('content')
<div class="page-header">
    <h2>Şifrə dəyiş</h2>
</div>

<div class="detail-card" style="max-width:480px">
    @if($errors->any())
        <div class="alert alert-error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('admin.password.update') }}">
        @csrf
        <div class="form-group">
            <label>Cari şifrə</label>
            <input type="password" name="current_password" required autofocus>
        </div>
        <div class="form-group">
            <label>Yeni şifrə</label>
            <input type="password" name="password" required minlength="6">
        </div>
        <div class="form-group">
            <label>Yeni şifrəni təkrarla</label>
            <input type="password" name="password_confirmation" required minlength="6">
        </div>
        <button type="submit" class="btn btn-primary">Yadda saxla</button>
    </form>
</div>
@endsection
