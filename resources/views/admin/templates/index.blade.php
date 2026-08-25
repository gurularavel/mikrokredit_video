@extends('layouts.admin')

@section('title', 'Mesaj Şablonları')

@section('content')
<div class="page-header">
    <h2><span class="eyebrow">Konfiqurasiya</span>Mesaj Şablonları</h2>
</div>

<div class="tabs-wrapper" role="tablist">
    <button class="tab-btn active" data-tab="sms" role="tab" aria-selected="true">SMS şablonları</button>
    <button class="tab-btn" data-tab="page" role="tab" aria-selected="false">Səhifə mesajları</button>
</div>

{{-- SMS Templates --}}
<div class="tab-panel" id="tab-sms">
    @foreach($sms as $tpl)
    <div class="detail-card template-card">
        <div class="template-header">
            <h3>{{ $tpl->label }}</h3>
            @if($tpl->placeholders && count($tpl->placeholders))
            <div class="placeholder-list">
                <span class="placeholder-hint">Dəyişənlər</span>
                @foreach($tpl->placeholders as $ph)
                    <code class="placeholder-tag" title="Kopyala" onclick="copyPlaceholder(this)">{{ $ph }}</code>
                @endforeach
            </div>
            @endif
        </div>
        <form method="POST" action="{{ route('admin.templates.update', $tpl) }}">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <textarea name="content" rows="4" class="template-textarea">{{ old('content_' . $tpl->key, $tpl->content) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Yadda saxla</button>
        </form>
    </div>
    @endforeach
</div>

{{-- Page Templates --}}
<div class="tab-panel" id="tab-page" style="display:none">
    @foreach($page as $tpl)
    <div class="detail-card template-card">
        <div class="template-header">
            <h3>{{ $tpl->label }}</h3>
            @if($tpl->placeholders && count($tpl->placeholders))
            <div class="placeholder-list">
                <span class="placeholder-hint">Dəyişənlər</span>
                @foreach($tpl->placeholders as $ph)
                    <code class="placeholder-tag" title="Kopyala" onclick="copyPlaceholder(this)">{{ $ph }}</code>
                @endforeach
            </div>
            @endif
        </div>
        <form method="POST" action="{{ route('admin.templates.update', $tpl) }}">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <textarea name="content" rows="{{ strlen($tpl->content) > 120 ? 4 : 2 }}" class="template-textarea">{{ old('content_' . $tpl->key, $tpl->content) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Yadda saxla</button>
        </form>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
document.querySelectorAll('.tab-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        document.querySelectorAll('.tab-btn').forEach(function (b) {
            b.classList.remove('active');
            b.setAttribute('aria-selected', 'false');
        });
        document.querySelectorAll('.tab-panel').forEach(function (p) { p.style.display = 'none'; });
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');
        document.getElementById('tab-' + btn.dataset.tab).style.display = 'block';
    });
});

function copyPlaceholder(el) {
    window.copyText(el.textContent.trim()).then(function () {
        el.classList.add('copied');
        setTimeout(function () { el.classList.remove('copied'); }, 900);
    });
}
</script>
@endpush
@endsection
