@extends('layouts.public')

@section('title', config('app.name', 'Video Kredit') . ' — Video Qeydiyyat Sistemi')
@section('masthead_note', 'Sistem aktiv')

@section('content')
<div class="split">

    <section class="split-intro reveal reveal-1">
        <span class="eyebrow">Video qeydiyyat sistemi</span>

        <h1 class="display">Kredit müraciətinin <em>video</em> təsdiqi.</h1>

        <p class="lede">
            Müştəriyə göndərilən birdəfəlik link, brauzerdə çəkilən qısa video və
            avtomatik arxivləşdirmə — hamısı bir axında.
        </p>

        <div class="perforation" aria-hidden="true"></div>

        <p class="lede" style="font-size:.9375rem">
            Müraciət linki SMS ilə göndərilir. Link yalnız bir dəfə açıla bilər və
            {{ (int) config('sms.expiry_minutes', 30) }} dəqiqədən sonra etibarını itirir.
        </p>
    </section>

    <section class="paper reveal reveal-2">
        <div class="paper-head">
            <h2>Necə işləyir</h2>
            <span class="stamp-no">v{{ config('app.version', '1.0') }}</span>
        </div>

        <ul class="spec-list" style="margin-top:0;border-top:0">
            <li style="padding-top:0">
                <span class="idx">01</span>
                <span>
                    <strong>Müraciət yaradılır</strong>
                    <span>Admin panelindən və ya merchant API-si vasitəsilə.</span>
                </span>
            </li>
            <li>
                <span class="idx">02</span>
                <span>
                    <strong>SMS göndərilir</strong>
                    <span>Müştəri birdəfəlik təsdiq linkini alır.</span>
                </span>
            </li>
            <li>
                <span class="idx">03</span>
                <span>
                    <strong>Video çəkilir</strong>
                    <span>Suflyör mətni ilə {{ (int) config('video.duration', 20) }} saniyəlik qeyd.</span>
                </span>
            </li>
            <li style="border-bottom:0">
                <span class="idx">04</span>
                <span>
                    <strong>Arxivə düşür</strong>
                    <span>Video sıxılır və müraciətə bağlanır.</span>
                </span>
            </li>
        </ul>

        <a href="{{ route('admin.login') }}" class="btn btn-secondary btn-block" style="margin-top:22px">
            Admin panelinə keçid
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="13 6 19 12 13 18"/></svg>
        </a>
    </section>

</div>
@endsection
