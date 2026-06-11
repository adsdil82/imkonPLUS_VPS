@extends('layouts.app')
@section('title', 'Sozlamalar')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Admin</a></li>
    <li class="breadcrumb-item active">Sozlamalar</li>
@endsection

@section('content')
<form method="POST" action="{{ route('admin.sozlamalar.saqlash') }}">
@csrf

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-gear me-2 text-secondary"></i>Tizim sozlamalari</h5>
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-save me-1"></i> Saqlash
    </button>
</div>

<div class="row g-3">

    {{-- ── BREND VA TIZIM NOMI ──────────────────────────────────── --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-tag me-2 text-primary"></i>Brend va tizim nomi</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Tizim nomi (Brend) <span class="text-danger">*</span></label>
                        <input type="text" name="brand_nomi" class="form-control @error('brand_nomi') is-invalid @enderror"
                               value="{{ old('brand_nomi', $soz['brand_nomi'] ?? 'NasiyaPro') }}"
                               placeholder="NasiyaPro">
                        @error('brand_nomi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Sidebar yuqorisida va sahifa sarlavhasida ko'rinadi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── KOMPANIYA REKVIZITLARI ───────────────────────────────── --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-building me-2 text-success"></i>Kompaniya rekvizitlari</h6>
                <small class="text-muted">Shartnoma, yuk xati, faktura va boshqa hujjatlarda chop etiladi</small>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-medium">Kompaniya nomi</label>
                        <input type="text" name="kompaniya_nomi" class="form-control"
                               value="{{ old('kompaniya_nomi', $soz['kompaniya_nomi'] ?? '') }}"
                               placeholder="MChJ «Tuyona»">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">INN (STIR)</label>
                        <input type="text" name="kompaniya_inn" class="form-control"
                               value="{{ old('kompaniya_inn', $soz['kompaniya_inn'] ?? '') }}"
                               placeholder="123456789">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Yuridik manzil</label>
                        <input type="text" name="kompaniya_manzil" class="form-control"
                               value="{{ old('kompaniya_manzil', $soz['kompaniya_manzil'] ?? '') }}"
                               placeholder="Toshkent sh., ...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Telefon</label>
                        <input type="text" name="kompaniya_telefon" class="form-control"
                               value="{{ old('kompaniya_telefon', $soz['kompaniya_telefon'] ?? '') }}"
                               placeholder="+998 90 123 45 67">
                    </div>

                    <div class="col-12"><hr class="my-1"></div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">Bank</label>
                        <input type="text" name="kompaniya_bank" class="form-control"
                               value="{{ old('kompaniya_bank', $soz['kompaniya_bank'] ?? '') }}"
                               placeholder="Ipak Yo'li bank">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">Hisob raqami</label>
                        <input type="text" name="kompaniya_hisob" class="form-control"
                               value="{{ old('kompaniya_hisob', $soz['kompaniya_hisob'] ?? '') }}"
                               placeholder="2020800001234567">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">MFO</label>
                        <input type="text" name="kompaniya_mfo" class="form-control"
                               value="{{ old('kompaniya_mfo', $soz['kompaniya_mfo'] ?? '') }}"
                               placeholder="00452">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Direktor F.I.O.</label>
                        <input type="text" name="kompaniya_direktor" class="form-control"
                               value="{{ old('kompaniya_direktor', $soz['kompaniya_direktor'] ?? '') }}"
                               placeholder="Karimov Jasur Aliyevich">
                    </div>
                </div>

                {{-- Rekvizit ko'rinishi --}}
                @php
                    $nom  = $soz['kompaniya_nomi'] ?? '';
                    $inn  = $soz['kompaniya_inn'] ?? '';
                    $tel  = $soz['kompaniya_telefon'] ?? '';
                    $bank = $soz['kompaniya_bank'] ?? '';
                    $his  = $soz['kompaniya_hisob'] ?? '';
                    $mfo  = $soz['kompaniya_mfo'] ?? '';
                @endphp
                @if($nom)
                <div class="mt-3 p-3 border rounded bg-light">
                    <div class="small text-muted mb-1">Hujjatlarda ko'rinishi:</div>
                    <div class="fw-bold">{{ $nom }}</div>
                    @if($inn) <div class="small">STIR: {{ $inn }}</div> @endif
                    @if($tel) <div class="small">Tel: {{ $tel }}</div> @endif
                    @if($bank && $his) <div class="small">{{ $bank }} | H/r: {{ $his }} | MFO: {{ $mfo }}</div> @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── INTERFEYS TEMASI ─────────────────────────────────────── --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-palette me-2 text-warning"></i>Interfeys temasi</h6>
                <small class="text-muted">Sidebar rangi va aksent rangi</small>
            </div>
            <div class="card-body">
                <input type="hidden" name="tema" id="tema-input" value="{{ old('tema', $soz['tema'] ?? 1) }}">
                <div class="row g-2">
                    @foreach($temalar as $id => $tema)
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="tema-karta {{ ($soz['tema'] ?? '1') == $id ? 'selected' : '' }}"
                             data-tema="{{ $id }}"
                             onclick="tanlaTemani({{ $id }})"
                             style="cursor:pointer; border-radius:10px; overflow:hidden; border:3px solid {{ ($soz['tema'] ?? '1') == $id ? $tema['accent'] : 'transparent' }}; transition:border 0.2s">
                            {{-- Preview --}}
                            <div style="display:flex;height:70px;">
                                <div style="width:40%;background:{{ $tema['sidebar'] }};display:flex;flex-direction:column;gap:4px;padding:6px 4px;">
                                    <div style="height:4px;background:{{ $tema['accent'] }};border-radius:2px;width:70%"></div>
                                    <div style="height:3px;background:rgba(255,255,255,0.3);border-radius:2px;width:90%"></div>
                                    <div style="height:3px;background:rgba(255,255,255,0.3);border-radius:2px;width:80%"></div>
                                    <div style="height:3px;background:rgba(255,255,255,0.3);border-radius:2px;width:85%"></div>
                                </div>
                                <div style="flex:1;background:#f8f9fa;padding:6px 4px;">
                                    <div style="height:4px;background:#e9ecef;border-radius:2px;margin-bottom:4px"></div>
                                    <div style="height:8px;background:{{ $tema['accent'] }};border-radius:2px;opacity:0.3"></div>
                                </div>
                            </div>
                            <div class="text-center py-1 small fw-medium" style="font-size:11px;background:#f8f9fa;">
                                {{ $tema['nomi'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── VALYUTA KURSLARI ─────────────────────────────────────── --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-currency-exchange me-2 text-warning"></i>Valyuta kurslari spravochnigi</h6>
                <small class="text-muted">Asosiy valyuta: <strong>UZS (so'm)</strong></small>
            </div>
            <div class="card-body">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Kurslar <strong>1 xorijiy valyuta = ???? so'm</strong> shaklida kiritiladi.
                    To'lov qilinayotganda sistem avtomatik so'm ekvivalentini hisoblaydi.
                </div>
                <div class="row g-3">
                    @php
                    $valyutalar = [
                        'usd' => ['nomi'=>'USD — AQSh dollari',   'bayroq'=>'🇺🇸'],
                        'eur' => ['nomi'=>'EUR — Yevro',           'bayroq'=>'🇪🇺'],
                        'rub' => ['nomi'=>'RUB — Rus rubli',       'bayroq'=>'🇷🇺'],
                        'cny' => ['nomi'=>'CNY — Xitoy yuani',     'bayroq'=>'🇨🇳'],
                    ];
                    @endphp
                    @foreach($valyutalar as $kod => $info)
                    <div class="col-md-6">
                        <div class="card border rounded-3 p-3" style="background:#fafbfc">
                            <div class="d-flex align-items-center mb-2 gap-2">
                                <span style="font-size:1.4rem">{{ $info['bayroq'] }}</span>
                                <strong>{{ $info['nomi'] }}</strong>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small mb-1 fw-medium text-success">
                                        <i class="bi bi-arrow-down-circle me-1"></i>Sotish kursi
                                        <small class="text-muted d-block" style="font-size:.7rem">Bizdan valyuta olish</small>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="{{ $kod }}_sotish_kurs"
                                               class="form-control" step="1" min="1"
                                               value="{{ old($kod.'_sotish_kurs', $soz[$kod.'_sotish_kurs'] ?? '') }}">
                                        <span class="input-group-text text-muted">so'm</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small mb-1 fw-medium text-danger">
                                        <i class="bi bi-arrow-up-circle me-1"></i>Olish kursi
                                        <small class="text-muted d-block" style="font-size:.7rem">Bizga valyuta berish</small>
                                    </label>
                                    <div class="input-group input-group-sm">
                                        <input type="number" name="{{ $kod }}_olish_kurs"
                                               class="form-control" step="1" min="1"
                                               value="{{ old($kod.'_olish_kurs', $soz[$kod.'_olish_kurs'] ?? '') }}">
                                        <span class="input-group-text text-muted">so'm</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4">
        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-save me-1"></i> Barcha sozlamalarni saqlash
            </button>
        </div>
    </div>

</div>
</form>


{{-- ═══ XABARNOMA SOZLAMALARI ════════════════════════════════════════════ --}}
<h5 class="fw-bold mb-3 mt-5 border-top pt-4">
    <i class="bi bi-bell me-2 text-warning"></i>Xabarnoma sozlamalari
</h5>
<div class="accordion" id="notifAccordion">

    {{-- SMS Sozlamalari --}}
    <div class="accordion-item border-0 shadow-sm mb-2">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseSms">
                <i class="bi bi-chat-dots me-2 text-warning"></i>SMS Sozlamalari
                @php $smsTest = \App\Models\NotificationSetting::get('sms','test_mode','1'); @endphp
                @if($smsTest === '1')
                <span class="badge bg-info ms-2 fw-normal" style="font-size:.65rem">test rejim</span>
                @else
                <span class="badge bg-success ms-2 fw-normal" style="font-size:.65rem">faol</span>
                @endif
            </button>
        </h2>
        <div id="collapseSms" class="accordion-collapse collapse" data-bs-parent="#notifAccordion">
            <div class="accordion-body">
                @php $smsSoz = \App\Models\NotificationSetting::where('channel','sms')->get()->keyBy('key'); @endphp
                <form method="POST" action="{{ route('admin.notif.sms.saqlash') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <label class="form-label small fw-medium">Provider</label>
                            <select name="provider" class="form-select form-select-sm">
                                <option value="test_mode" {{ ($smsSoz['provider']->value??'test_mode')==='test_mode'?'selected':'' }}>Test Mode</option>
                                <option value="eskiz"     {{ ($smsSoz['provider']->value??'')==='eskiz'    ?'selected':'' }}>Eskiz.uz</option>
                                <option value="playmobile"{{ ($smsSoz['provider']->value??'')==='playmobile'?'selected':'' }}>PlayMobile</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small fw-medium">Sender ID</label>
                            <input type="text" name="sender_id" class="form-control form-control-sm"
                                   value="{{ $smsSoz['sender_id']->value ?? 'NasiyaPro' }}">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label small fw-medium">Test telefon</label>
                            <input type="text" name="test_phone" class="form-control form-control-sm"
                                   value="{{ $smsSoz['test_phone']->value ?? '' }}" placeholder="+998901234567">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Login</label>
                            <input type="text" name="login" class="form-control form-control-sm"
                                   value="{{ $smsSoz['login']->value ?? '' }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Parol/Token</label>
                            <input type="password" name="password" class="form-control form-control-sm"
                                   placeholder="{{ ($smsSoz['password']->value??'')?'••••••••':'Kiriting' }}">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3 align-items-center">
                        <div class="form-check form-switch me-3">
                            <input class="form-check-input" type="checkbox" name="test_mode" id="adm-sms-test"
                                   {{ ($smsSoz['test_mode']->value??'1')==='1'?'checked':'' }}>
                            <label class="form-check-label small" for="adm-sms-test">Test rejimi</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i>Saqlash</button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="adminTestSms()">
                            <i class="bi bi-send me-1"></i>Test SMS
                        </button>
                        <span id="adm-sms-natija" class="small ms-2"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Telegram Sozlamalari --}}
    <div class="accordion-item border-0 shadow-sm mb-2">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseTelegram">
                <i class="bi bi-telegram me-2 text-info"></i>Telegram Sozlamalari
            </button>
        </h2>
        <div id="collapseTelegram" class="accordion-collapse collapse" data-bs-parent="#notifAccordion">
            <div class="accordion-body">
                @php $tgSoz = \App\Models\NotificationSetting::where('channel','telegram')->get()->keyBy('key'); @endphp
                <form method="POST" action="{{ route('admin.notif.telegram.saqlash') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Bot Token</label>
                            <input type="password" name="bot_token" class="form-control form-control-sm"
                                   placeholder="{{ ($tgSoz['bot_token']->value??'')?'••••••••':'Bot token' }}">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small fw-medium">Test Chat ID</label>
                            <input type="text" name="test_chat_id" class="form-control form-control-sm"
                                   value="{{ $tgSoz['test_chat_id']->value ?? '' }}" placeholder="123456789">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i>Saqlash</button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="adminTestTelegram()">
                            <i class="bi bi-send me-1"></i>Test xabar
                        </button>
                        <span id="adm-tg-natija" class="small ms-2"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Email Sozlamalari --}}
    <div class="accordion-item border-0 shadow-sm mb-2">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseEmail">
                <i class="bi bi-envelope me-2 text-primary"></i>Email Sozlamalari
            </button>
        </h2>
        <div id="collapseEmail" class="accordion-collapse collapse" data-bs-parent="#notifAccordion">
            <div class="accordion-body">
                @php $emSoz = \App\Models\NotificationSetting::where('channel','email')->get()->keyBy('key'); @endphp
                <form method="POST" action="{{ route('admin.notif.email.saqlash') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-4"><label class="form-label small fw-medium">Host</label>
                            <input type="text" name="host" class="form-control form-control-sm" value="{{ $emSoz['host']->value??'' }}" placeholder="smtp.gmail.com"></div>
                        <div class="col-sm-2"><label class="form-label small fw-medium">Port</label>
                            <input type="number" name="port" class="form-control form-control-sm" value="{{ $emSoz['port']->value??'587' }}"></div>
                        <div class="col-sm-3"><label class="form-label small fw-medium">Username</label>
                            <input type="text" name="username" class="form-control form-control-sm" value="{{ $emSoz['username']->value??'' }}"></div>
                        <div class="col-sm-3"><label class="form-label small fw-medium">Parol</label>
                            <input type="password" name="password" class="form-control form-control-sm" placeholder="{{ ($emSoz['password']->value??'')?'••••••••':'' }}"></div>
                        <div class="col-sm-6"><label class="form-label small fw-medium">From Address</label>
                            <input type="email" name="from_address" class="form-control form-control-sm" value="{{ $emSoz['from_address']->value??'' }}" placeholder="noreply@nasiyapro.uz"></div>
                        <div class="col-sm-6"><label class="form-label small fw-medium">Test Email</label>
                            <input type="email" name="test_email" class="form-control form-control-sm" value="{{ $emSoz['test_email']->value??'' }}"></div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i>Saqlash</button>
                        <span id="adm-em-natija" class="small ms-2"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Gibrid Pochta Sozlamalari --}}
    <div class="accordion-item border-0 shadow-sm mb-2">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed fw-bold" type="button"
                    data-bs-toggle="collapse" data-bs-target="#collapseHybrid">
                <i class="bi bi-envelope-paper me-2" style="color:#8b5cf6"></i>Gibrid Pochta Sozlamalari
            </button>
        </h2>
        <div id="collapseHybrid" class="accordion-collapse collapse" data-bs-parent="#notifAccordion">
            <div class="accordion-body">
                @php $hmSoz = \App\Models\NotificationSetting::where('channel','hybrid_mail')->get()->keyBy('key'); @endphp
                <form method="POST" action="{{ route('admin.notif.hybrid.saqlash') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-sm-6"><label class="form-label small fw-medium">API URL</label>
                            <input type="url" name="api_url" class="form-control form-control-sm" value="{{ $hmSoz['api_url']->value??'' }}"></div>
                        <div class="col-sm-6"><label class="form-label small fw-medium">Login / Client ID</label>
                            <input type="text" name="login" class="form-control form-control-sm" value="{{ $hmSoz['login']->value??'' }}"></div>
                        <div class="col-sm-6"><label class="form-label small fw-medium">Token</label>
                            <input type="password" name="token" class="form-control form-control-sm" placeholder="{{ ($hmSoz['token']->value??'')?'••••••••':'' }}"></div>
                        <div class="col-sm-6"><label class="form-label small fw-medium">Jo'natuvchi nomi</label>
                            <input type="text" name="sender_name" class="form-control form-control-sm" value="{{ $hmSoz['sender_name']->value??'NasiyaPro' }}"></div>
                    </div>
                    <div class="d-flex gap-2 mt-3 align-items-center">
                        <div class="form-check form-switch me-2">
                            <input class="form-check-input" type="checkbox" name="test_mode"
                                   {{ ($hmSoz['test_mode']->value??'1')==='1'?'checked':'' }}>
                            <label class="form-check-label small">Test rejimi</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i>Saqlash</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>{{-- /accordion --}}

@endsection

@push('scripts')
<script>
function adminTestSms() {
    var el = document.getElementById('adm-sms-natija');
    el.textContent = '...';
    fetch('{{ route("xabarnoma.sms.test") }}', {
        method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}
    }).then(r=>r.json()).then(d=>{
        el.className='small ms-2 '+(d.status==='test'?'text-success':'text-danger');
        el.textContent=d.status==='test'?'Test yuborildi!':('Xato: '+(d.error||''));
    });
}
function adminTestTelegram() {
    var el = document.getElementById('adm-tg-natija');
    el.textContent = '...';
    fetch('{{ route("xabarnoma.telegram.test") }}', {
        method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'}
    }).then(r=>r.json()).then(d=>{
        el.className='small ms-2 '+(d.ok?'text-success':'text-danger');
        el.textContent=d.ok?'Test yuborildi!':('Xato: '+(d.result?.description||d.error||''));
    });
}
function tanlaTemani(id) {
    document.getElementById('tema-input').value = id;
    document.querySelectorAll('.tema-karta').forEach(k => {
        k.style.border = '3px solid transparent';
    });
    const temalar = @json($temalar);
    const karta = document.querySelector(`[data-tema="${id}"]`);
    if (karta) {
        karta.style.border = `3px solid ${temalar[id].accent}`;
    }
}
</script>
@endpush
