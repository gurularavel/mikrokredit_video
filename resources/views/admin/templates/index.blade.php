@extends('layouts.admin')

@section('title', 'Mesaj Şablonları')

@section('content')
<div class="page-header">
    <h2>Mesaj Şablonları</h2>
</div>

<div class="tabs-wrapper">
    <button class="tab-btn active" data-tab="sms">SMS Şablonları</button>
    <button class="tab-btn" data-tab="page">Səhifə Mesajları</button>
</div>

{{-- SMS Templates --}}
<div class="tab-panel" id="tab-sms">
    @foreach($sms as $tpl)
    <div class="detail-card template-card">
        <div class="template-header">
            <h3>{{ $tpl->label }}</h3>
            @if($tpl->placeholders)
            <div class="placeholder-list">
                <span class="placeholder-hint">İstifadə edilə bilən dəyişənlər:</span>
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
                <span class="placeholder-hint">İstifadə edilə bilən dəyişənlər:</span>
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

<style>
.tabs-wrapper { display:flex; gap:4px; margin-bottom:20px; }
.tab-btn {
    padding:8px 24px; border:1.5px solid #E2E8F0; background:#F8FAFC;
    border-radius:8px 8px 0 0; font-size:.875rem; font-weight:600;
    cursor:pointer; color:#475569; transition:all .15s;
    font-family:'Inter', -apple-system, sans-serif;
}
.tab-btn.active { background:#1560BD; color:#fff; border-color:#1560BD; box-shadow:0 2px 8px rgba(21,96,189,.3); }
.template-card { margin-bottom:16px; }
.template-header { margin-bottom:12px; }
.template-header h3 { font-size:.9375rem; font-weight:600; color:#0F172A; margin-bottom:6px; }
.placeholder-list { display:flex; flex-wrap:wrap; align-items:center; gap:6px; }
.placeholder-hint { font-size:.75rem; color:#94A3B8; font-weight:500; text-transform:uppercase; letter-spacing:.04em; }
.placeholder-tag {
    background:#EBF3FF; color:#1560BD; border:1px solid #93C5FD;
    border-radius:4px; padding:2px 8px; font-size:.79rem; font-weight:600;
    cursor:pointer; transition:all .15s; font-family:inherit;
}
.placeholder-tag:hover { background:#D2E4FA; border-color:#1560BD; }
.template-textarea {
    width:100%; padding:10px 13px;
    border:1.5px solid #E2E8F0; border-radius:8px;
    font-size:.9375rem; font-family:'Inter', -apple-system, sans-serif;
    resize:vertical; transition:border-color .2s, box-shadow .2s; line-height:1.6;
    background:#FAFCFF; color:#0F172A;
}
.template-textarea:focus { outline:none; border-color:#1560BD; box-shadow:0 0 0 3px rgba(21,96,189,.12); }
</style>

<script>
document.querySelectorAll('.tab-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
        document.querySelectorAll('.tab-panel').forEach(function(p) { p.style.display = 'none'; });
        btn.classList.add('active');
        document.getElementById('tab-' + btn.dataset.tab).style.display = 'block';
    });
});

function copyPlaceholder(el) {
    navigator.clipboard.writeText(el.textContent.trim()).then(function() {
        var orig = el.style.background;
        el.style.background = '#c7d2fe';
        setTimeout(function() { el.style.background = orig; }, 600);
    });
}
</script>
@endsection
