@extends('layouts.app')

@section('title', 'Yangi shartnoma')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kreditlar.index') }}">Shartnomalar</a></li>
    <li class="breadcrumb-item active">Yangi shartnoma</li>
@endsection

@section('content')

<div class="card border-0 shadow-sm">
    <div class="card-header">
        <h6 class="mb-0"><i class="bi bi-file-earmark-plus me-1"></i> Yangi nasiya shartnomasi</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('kreditlar.store') }}" id="kredit-form">
            @csrf

            {{-- ── 1. Mijoz tanlash ─────────────────────────────────── --}}
            <h6 class="fw-bold text-muted mb-2 border-bottom pb-1">1. Mijoz</h6>
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Mijoz <span class="text-danger">*</span></label>
                    <input type="hidden" name="mijoz_id" id="mijoz_id"
                           value="{{ $mijoz?->id }}" required>
                    <div class="input-group">
                        <input type="text" id="mijoz-tanlangan" class="form-control"
                               placeholder="Mijoz tanlanmagan — qidirish uchun bosing..."
                               value="{{ $mijoz ? $mijoz->familiya . ' ' . $mijoz->ism : '' }}"
                               readonly style="cursor:pointer;background:#fff"
                               onclick="mijozModalOch()">
                        <button type="button" class="btn btn-primary" onclick="mijozModalOch()"
                                title="Mijoz qidirish va tanlash">
                            <i class="bi bi-person-search me-1"></i>
                            <span class="d-none d-sm-inline">Qidirish</span>
                        </button>
                    </div>
                    <div id="mijoz-info" class="small text-muted mt-1">
                        @if($mijoz)
                            <i class="bi bi-check-circle text-success me-1"></i>
                            {{ $mijoz->familiya }} {{ $mijoz->ism }}
                            &nbsp;·&nbsp; {{ $mijoz->telefon }}
                            &nbsp;·&nbsp; {{ $mijoz->passport_tolik }}
                        @else
                            <span class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>Mijoz tanlanmagan</span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Filial <span class="text-danger">*</span></label>
                    <select name="filial_id" class="form-select @error('filial_id') is-invalid @enderror"
                            {{ count($filiallar) === 1 ? 'disabled' : '' }}>
                        @foreach($filiallar as $f)
                            <option value="{{ $f->id }}"
                                {{ old('filial_id', count($filiallar) === 1 ? $filiallar->first()->id : '') == $f->id ? 'selected' : '' }}>
                                {{ $f->nomi }}
                            </option>
                        @endforeach
                    </select>
                    @if(count($filiallar) === 1)
                        <input type="hidden" name="filial_id" value="{{ $filiallar->first()->id }}">
                    @endif
                </div>
            </div>

            {{-- ── 2. Moliyaviy ma'lumotlar ──────────────────────────── --}}
            <h6 class="fw-bold text-muted mb-2 border-bottom pb-1">2. Moliyaviy ma'lumotlar</h6>
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <label class="form-label fw-medium">Jami summa <span class="text-danger">*</span></label>
                    <input type="number" name="jami_summa" id="jami_summa"
                           class="form-control @error('jami_summa') is-invalid @enderror"
                           value="{{ old('jami_summa', 0) }}" min="0" step="1000"
                           oninput="hisoblash()">
                    @error('jami_summa')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-medium">Boshlang'ich to'lov <span class="text-danger">*</span></label>
                    <input type="number" name="boshlangich_tolov" id="boshlangich_tolov"
                           class="form-control @error('boshlangich_tolov') is-invalid @enderror"
                           value="{{ old('boshlangich_tolov', 0) }}" min="0" step="1000"
                           oninput="hisoblash()">
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-medium">Nasiya summasi</label>
                    <input type="text" id="kredit_summa_display" class="form-control bg-body-secondary" readonly>
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-medium">Muddat (oy) <span class="text-danger">*</span></label>
                    <select name="muddati_oy" id="muddati_oy"
                            class="form-select @error('muddati_oy') is-invalid @enderror"
                            onchange="hisoblash()">
                        @foreach([1,2,3,4,5,6,9,12,18,24,36] as $oy)
                            <option value="{{ $oy }}" {{ old('muddati_oy', 12) == $oy ? 'selected' : '' }}>
                                {{ $oy }} oy
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-medium">Yillik foiz stavkasi (%)</label>
                    <input type="number" name="foiz_stavka" id="foiz_stavka"
                           class="form-control" value="{{ old('foiz_stavka', 0) }}"
                           min="0" max="100" step="0.1" oninput="hisoblash()">
                    <div class="form-text">0 = foizsiz</div>
                </div>
                <div class="col-sm-4">
                    <label class="form-label fw-medium">Oylik to'lov</label>
                    <input type="text" id="oylik_display" class="form-control bg-body-secondary" readonly>
                </div>
            </div>

            {{-- ── 3. Sanalar ────────────────────────────────────────── --}}
            <h6 class="fw-bold text-muted mb-2 border-bottom pb-1">3. Muddatlar</h6>
            <div class="row g-3 mb-4">
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Boshlanish sanasi <span class="text-danger">*</span></label>
                    <input type="date" name="boshlanish_sana" id="boshlanish_sana"
                           class="form-control @error('boshlanish_sana') is-invalid @enderror"
                           value="{{ old('boshlanish_sana', date('Y-m-d')) }}"
                           onchange="tugashSanaHisoblash()">
                </div>
                <div class="col-sm-6">
                    <label class="form-label fw-medium">Tugash sanasi <span class="text-danger">*</span></label>
                    <input type="date" name="tugash_sana" id="tugash_sana"
                           class="form-control @error('tugash_sana') is-invalid @enderror"
                           value="{{ old('tugash_sana') }}">
                </div>
            </div>

            {{-- ── 4. Tovarlar ───────────────────────────────────────── --}}
            <h6 class="fw-bold text-muted mb-2 border-bottom pb-1">4. Tovarlar</h6>
            <div id="tovarlar-container">
                <div class="tovar-qator row g-2 mb-2 align-items-center">
                    <div class="col-sm-4">
                        <div class="input-group input-group-sm">
                            <input type="text" name="tovarlar[0][nomi]" class="form-control form-control-sm tovar-nomi-inp"
                                   placeholder="Tovar nomi" required>
                            <button type="button" class="btn btn-outline-primary btn-sm tovar-izlash-btn"
                                    onclick="tovarModalOch(this)" title="Ombordan tovar tanlash">
                                <i class="bi bi-tv"></i><i class="bi bi-plus-lg" style="font-size:.65rem;vertical-align:super"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <input type="number" name="tovarlar[0][soni]" class="form-control form-control-sm tovar-soni"
                               placeholder="Soni" value="1" min="1" oninput="tovarJamiHisoblash(this)">
                    </div>
                    <div class="col-sm-3">
                        <input type="number" name="tovarlar[0][narx]" class="form-control form-control-sm tovar-narx"
                               placeholder="Narx" value="0" min="0" step="1000" oninput="tovarJamiHisoblash(this)">
                    </div>
                    <div class="col-sm-1">
                        <input type="text" class="form-control form-control-sm bg-body-secondary tovar-jami"
                               placeholder="Jami" readonly>
                    </div>
                    <div class="col-sm-1">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="tovarOchir(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="tovarQosh()">
                <i class="bi bi-plus me-1"></i> Tovar qo'shish
            </button>

            {{-- ── 5. Kafil ──────────────────────────────────────────── --}}
            <h6 class="fw-bold text-muted mb-2 border-bottom pb-1 mt-4">5. Kafil (ixtiyoriy)</h6>
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <label class="form-label">F.I.O.</label>
                    <input type="text" name="kafil_ism" class="form-control"
                           value="{{ old('kafil_ism') }}">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Telefon</label>
                    <input type="text" name="kafil_telefon" class="form-control"
                           value="{{ old('kafil_telefon') }}">
                </div>
                <div class="col-sm-4">
                    <label class="form-label">Manzil</label>
                    <input type="text" name="kafil_manzil" class="form-control"
                           value="{{ old('kafil_manzil') }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Izoh</label>
                <textarea name="izoh" class="form-control" rows="2">{{ old('izoh') }}</textarea>
            </div>

            <hr>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Shartnomani saqlash
                </button>
                <a href="{{ route('kreditlar.index') }}" class="btn btn-outline-secondary">Bekor qilish</a>
            </div>
        </form>
    </div>
</div>



{{-- ═══ MIJOZ IZLASH MODAL ════════════════════════════════════════════ --}}
<div class="modal fade" id="mijozIzlashModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">

      {{-- Header --}}
      <div class="modal-header py-2" style="background:linear-gradient(135deg,#15803d,#16a34a)">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-person-search text-white fs-5"></i>
          <h6 class="modal-title text-white fw-bold mb-0">Mijoz tanlash</h6>
        </div>
        {{-- Qidiruv (markazda) --}}
        <div class="mx-3 flex-grow-1" style="max-width:320px">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-0">
              <i class="bi bi-search text-success"></i>
            </span>
            <input type="text" id="mijoz-modal-qidiruv" class="form-control border-0"
                   placeholder="Ism, telefon, passport..." autocomplete="off">
            <div id="mijoz-modal-spinner" class="input-group-text bg-white border-0 d-none">
              <div class="spinner-border spinner-border-sm text-success"></div>
            </div>
          </div>
        </div>
        {{-- Yangi mijoz tugmasi -- header o'ngida -- har doim ko'rinadi --}}
        @if(Auth::user()->isMenejerYoki())
        <a href="{{ route('mijozlar.create') }}" target="_blank"
           class="btn btn-light btn-sm me-2 fw-bold"
           title="Yangi oynada yangi mijoz yaratish formasini oching">
          <i class="bi bi-person-plus me-1 text-success"></i>Yangi mijoz
        </a>
        @endif
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      {{-- Body --}}
      <div class="modal-body p-0">
        <div id="mijoz-modal-body-inner" style="min-height:200px">
          <div id="mijoz-modal-hint" class="text-center text-muted py-5">
            <i class="bi bi-search fs-2 d-block mb-2 opacity-25"></i>
            Qidirish uchun ism yoki telefon kiriting...
          </div>
          <div id="mijoz-modal-empty" class="text-center text-muted py-4 d-none">
            <i class="bi bi-person-x fs-2 d-block mb-2 opacity-25"></i>
            <div>Mijoz topilmadi</div>
            @if(Auth::user()->isMenejerYoki())
            <a href="{{ route('mijozlar.create') }}" target="_blank"
               class="btn btn-success btn-sm mt-3">
              <i class="bi bi-person-plus me-1"></i>Yangi mijoz kiritish
            </a>
            <div class="text-muted small mt-2">
              Yangi oynada yaratingizdan so'ng bu sahifani yangilang
            </div>
            @endif
          </div>
          <table class="table table-hover table-sm mb-0 d-none" id="mijoz-modal-jadval">
            <thead class="table-light sticky-top">
              <tr>
                <th>F.I.O.</th>
                <th>Telefon</th>
                <th>Passport</th>
                <th>Filial</th>
                <th class="text-center">Holat</th>
              </tr>
            </thead>
            <tbody id="mijoz-modal-tbody"></tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer py-2 justify-content-between">
        <div>
          @if(Auth::user()->isMenejerYoki())
          <a href="{{ route('mijozlar.create') }}" target="_blank"
             class="btn btn-sm btn-outline-success">
            <i class="bi bi-person-plus me-1"></i>Yangi mijoz yaratish
          </a>
          @endif
          <small class="text-muted ms-2">
            <i class="bi bi-hand-index me-1"></i>2 marta bosing — tanlash
          </small>
        </div>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Yopish</button>
      </div>

    </div>
  </div>
</div>
{{-- ═══ TOVAR IZLASH MODAL ════════════════════════════════════════════ --}}
<div class="modal fade" id="tovarIzlashModal" tabindex="-1" aria-labelledby="tovarModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-lg">

      {{-- Header --}}
      <div class="modal-header py-2" style="background:linear-gradient(135deg,#1e3a5f,#2563eb)">
        <div class="d-flex align-items-center gap-2 flex-grow-1">
          <i class="bi bi-tv text-white fs-5"></i>
          <h6 class="modal-title text-white fw-bold mb-0" id="tovarModalLabel">Ombordan tovar tanlash</h6>
        </div>
        <div class="ms-3 flex-grow-1" style="max-width:320px">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-0">
              <i class="bi bi-search text-primary"></i>
            </span>
            <input type="text" id="tovar-modal-qidiruv" class="form-control border-0"
                   placeholder="Tovar nomini kiriting..." autocomplete="off">
            <button type="button" class="btn btn-outline-light btn-sm" id="tovar-modal-tozala"
                    onclick="document.getElementById('tovar-modal-qidiruv').value='';tovarModalFiltr('')"
                    title="Tozalash">
              <i class="bi bi-x-lg"></i>
            </button>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
      </div>

      {{-- Guruh tablar --}}
      <div class="modal-body p-0">
        <div class="px-3 pt-2 pb-0 border-bottom">
          <ul class="nav nav-tabs nav-tabs-sm border-0 gap-1" id="tovar-guruh-tablar">
            <li class="nav-item">
              <button class="nav-link active py-1 px-2 small fw-bold" data-guruh="0" onclick="tovarGuruhFilter(0,this)">
                Hammasi
              </button>
            </li>
            @foreach($tovarGuruhlar as $g)
            <li class="nav-item">
              <button class="nav-link py-1 px-2 small" data-guruh="{{ $g->id }}" onclick="tovarGuruhFilter({{ $g->id }},this)">
                {{ $g->nomi }}
                <span class="badge bg-secondary ms-1" style="font-size:.65rem">{{ $g->tovarlar->count() }}</span>
              </button>
            </li>
            @endforeach
          </ul>
        </div>

        {{-- Tovar jadval --}}
        <div style="max-height:420px;overflow-y:auto">
          <table class="table table-hover table-sm mb-0" id="tovar-modal-jadval">
            <thead class="table-light sticky-top">
              <tr>
                <th style="width:50%">Tovar nomi</th>
                <th style="width:15%" class="text-center">Qoldiq</th>
                <th style="width:15%" class="text-end">Narx (so'm)</th>
                <th style="width:20%" class="text-center">Birlik</th>
              </tr>
            </thead>
            <tbody id="tovar-modal-tbody">
              @foreach($tovarGuruhlar as $g)
                @foreach($g->tovarlar as $t)
                <tr class="tovar-modal-qator"
                    data-id="{{ $t->id }}"
                    data-nomi="{{ $t->nomi }}"
                    data-narx="{{ (int)$t->sotish_narx }}"
                    data-qoldiq="{{ (float)$t->qoldiq }}"
                    data-birlik="{{ $t->birlik }}"
                    data-guruh="{{ $g->id }}"
                    style="cursor:pointer"
                    ondblclick="tovarModalTanlash(this)"
                    title="2 marta bosing — qatorga qo'shish">
                  <td>
                    <div class="fw-medium small">{{ $t->nomi }}</div>
                    <div class="text-muted" style="font-size:.7rem">{{ $g->nomi }}</div>
                  </td>
                  <td class="text-center small">
                    @if($t->qoldiq > 0)
                      <span class="badge bg-success bg-opacity-15 text-success fw-bold">
                        {{ number_format($t->qoldiq, 0) }}
                      </span>
                    @else
                      <span class="badge bg-danger bg-opacity-15 text-danger">0</span>
                    @endif
                  </td>
                  <td class="text-end small fw-medium">
                    {{ number_format($t->sotish_narx, 0, '.', ' ') }}
                  </td>
                  <td class="text-center text-muted small">{{ $t->birlik }}</td>
                </tr>
                @endforeach
              @endforeach
            </tbody>
          </table>
          <div id="tovar-modal-empty" class="text-center text-muted py-5 d-none">
            <i class="bi bi-search fs-2 d-block mb-2 opacity-25"></i>
            Tovar topilmadi
          </div>
        </div>
      </div>

      <div class="modal-footer py-2 justify-content-between">
        <small class="text-muted">
          <i class="bi bi-hand-index me-1"></i>2 marta bosing — qatorga qo'shiladi
          &nbsp;|&nbsp; <span id="tovar-modal-soni">0</span> ta tovar
        </small>
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Yopish</button>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
// ─── ASOSIY FORM FUNKSIYALARI ────────────────────────────────────────

let tovarIndex = 1;

// Moliyaviy hisoblash
function hisoblash() {
    const jami   = parseFloat($('#jami_summa').val()) || 0;
    const oldin  = parseFloat($('#boshlangich_tolov').val()) || 0;
    const kredit = Math.max(0, jami - oldin);
    const muddat = parseInt($('#muddati_oy').val()) || 1;
    const foiz   = parseFloat($('#foiz_stavka').val()) || 0;

    let oylik = kredit / muddat;
    if (foiz > 0) {
        oylik = (kredit + kredit * foiz / 100) / muddat;
    }
    $('#kredit_summa_display').val(formatSon(kredit));
    $('#oylik_display').val(formatSon(Math.round(oylik)));
    tugashSanaHisoblash();
}

function tugashSanaHisoblash() {
    const bosh   = $('#boshlanish_sana').val();
    const muddat = parseInt($('#muddati_oy').val()) || 1;
    if (!bosh) return;
    const dt = new Date(bosh);
    dt.setMonth(dt.getMonth() + muddat - 1);
    const y = dt.getFullYear();
    const m = String(dt.getMonth() + 1).padStart(2, '0');
    const d = String(dt.getDate()).padStart(2, '0');
    $('#tugash_sana').val(`${y}-${m}-${d}`);
}

function formatSon(n) {
    return n.toLocaleString('uz-UZ');
}

// Tovar qatorlari
function tovarQosh() {
    const i   = tovarIndex++;
    const row = `
    <div class="tovar-qator row g-2 mb-2 align-items-center">
        <div class="col-sm-4">
            <div class="input-group input-group-sm">
                <input type="text" name="tovarlar[${i}][nomi]"
                       class="form-control form-control-sm tovar-nomi-inp"
                       placeholder="Tovar nomi" required>
                <button type="button" class="btn btn-outline-primary btn-sm tovar-izlash-btn"
                        onclick="tovarModalOch(this)" title="Ombordan tovar tanlash">
                    <i class="bi bi-tv"></i><i class="bi bi-plus-lg" style="font-size:.65rem;vertical-align:super"></i>
                </button>
            </div>
        </div>
        <div class="col-sm-2">
            <input type="number" name="tovarlar[${i}][soni]"
                   class="form-control form-control-sm tovar-soni"
                   placeholder="Soni" value="1" min="1" oninput="tovarJamiHisoblash(this)">
        </div>
        <div class="col-sm-3">
            <input type="number" name="tovarlar[${i}][narx]"
                   class="form-control form-control-sm tovar-narx"
                   placeholder="Narx" value="0" min="0" step="1000" oninput="tovarJamiHisoblash(this)">
        </div>
        <div class="col-sm-1">
            <input type="text" class="form-control form-control-sm bg-body-secondary tovar-jami"
                   placeholder="Jami" readonly>
        </div>
        <div class="col-sm-1">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="tovarOchir(this)">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>`;
    $('#tovarlar-container').append(row);
}

function tovarOchir(btn) {
    if ($('.tovar-qator').length > 1) {
        $(btn).closest('.tovar-qator').remove();
    }
}

function tovarJamiHisoblash(inp) {
    const row  = $(inp).closest('.tovar-qator');
    const soni = parseFloat(row.find('.tovar-soni').val()) || 0;
    const narx = parseFloat(row.find('.tovar-narx').val()) || 0;
    row.find('.tovar-jami').val(formatSon(soni * narx));
}

// Init
hisoblash();

// ─── MIJOZ MODAL ────────────────────────────────────────────────────
var _mijozModal = null;
var _mijozTimer = null;

function mijozModalOch() {
    if (!_mijozModal) {
        _mijozModal = new bootstrap.Modal(document.getElementById('mijozIzlashModal'));
    }
    document.getElementById('mijoz-modal-qidiruv').value = '';
    document.getElementById('mijoz-modal-jadval').classList.add('d-none');
    document.getElementById('mijoz-modal-empty').classList.add('d-none');
    document.getElementById('mijoz-modal-hint').classList.remove('d-none');
    document.getElementById('mijoz-modal-tbody').innerHTML = '';
    _mijozModal.show();
    setTimeout(function() {
        document.getElementById('mijoz-modal-qidiruv').focus();
    }, 400);
}

function mijozModalTanlash(row) {
    document.getElementById('mijoz_id').value       = row.dataset.id;
    document.getElementById('mijoz-tanlangan').value = row.dataset.fio;
    document.getElementById('mijoz-info').innerHTML  =
        '<i class="bi bi-check-circle text-success me-1"></i>' +
        '<strong>' + row.dataset.fio + '</strong>' +
        ' &nbsp;&middot;&nbsp; ' + row.dataset.telefon +
        ' &nbsp;&middot;&nbsp; ' + row.dataset.passport;
    if (_mijozModal) _mijozModal.hide();
}

document.addEventListener('DOMContentLoaded', function() {
    var mqEl = document.getElementById('mijoz-modal-qidiruv');
    if (!mqEl) return;

    mqEl.addEventListener('input', function() {
        clearTimeout(_mijozTimer);
        var q = this.value.trim();
        if (q.length < 2) {
            document.getElementById('mijoz-modal-jadval').classList.add('d-none');
            document.getElementById('mijoz-modal-empty').classList.add('d-none');
            document.getElementById('mijoz-modal-hint').classList.remove('d-none');
            document.getElementById('mijoz-modal-tbody').innerHTML = '';
            return;
        }
        document.getElementById('mijoz-modal-spinner').classList.remove('d-none');
        _mijozTimer = setTimeout(function() { mijozQidirAjax(q); }, 200);
    });

    mqEl.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            // Agar 1 ta natija qolsa - tanlash
            var rows = document.querySelectorAll('#mijoz-modal-tbody tr');
            if (rows.length === 1) {
                mijozModalTanlash(rows[0]);
                return;
            }
            // Darhol qidiruv (timer kutmasdan)
            clearTimeout(_mijozTimer);
            var q = mqEl.value.trim();
            if (q.length >= 2) {
                document.getElementById('mijoz-modal-spinner').classList.remove('d-none');
                mijozQidirAjax(q);
            }
        }
    });
});

function mijozQidirAjax(q) {
    var filialId = document.querySelector('[name=filial_id]') ?
                  document.querySelector('[name=filial_id]').value : '';
    $.getJSON('{{ route("mijozlar.ajax.qidiruv") }}', { q: q, filial_id: filialId })
        .done(function(data) {
            document.getElementById('mijoz-modal-spinner').classList.add('d-none');
            var tbody  = document.getElementById('mijoz-modal-tbody');
            var jadval = document.getElementById('mijoz-modal-jadval');
            var empty  = document.getElementById('mijoz-modal-empty');
            var hint   = document.getElementById('mijoz-modal-hint');

            hint.classList.add('d-none');
            if (data.length === 0) {
                jadval.classList.add('d-none');
                empty.classList.remove('d-none');
                tbody.innerHTML = '';
                return;
            }
            empty.classList.add('d-none');
            jadval.classList.remove('d-none');

            tbody.innerHTML = data.map(function(m) {
                var badge = m.holat === 'faol'
                    ? '<span class="badge bg-success bg-opacity-15 text-success" style="font-size:.65rem">Faol</span>'
                    : '<span class="badge bg-secondary bg-opacity-15 text-secondary" style="font-size:.65rem">Nofaol</span>';
                return '<tr class="mijoz-modal-qator" style="cursor:pointer"' +
                    ' data-id="' + m.id + '"' +
                    ' data-fio="' + (m.fio || '').replace(/"/g, "'") + '"' +
                    ' data-telefon="' + (m.telefon || '') + '"' +
                    ' data-passport="' + (m.passport || '') + '"' +
                    ' ondblclick="mijozModalTanlash(this)"' +
                    ' title="2 marta bosing">' +
                    '<td><div class="fw-medium small">' + m.fio + '</div></td>' +
                    '<td class="small">' + m.telefon + '</td>' +
                    '<td class="small text-muted">' + m.passport + '</td>' +
                    '<td class="small text-muted">' + (m.filial || '') + '</td>' +
                    '<td class="text-center">' + badge + '</td>' +
                    '</tr>';
            }).join('');
        })
        .fail(function() {
            document.getElementById('mijoz-modal-spinner').classList.add('d-none');
        });
}

// ─── TOVAR MODAL ────────────────────────────────────────────────────
var _activeTovarRow = null;
var _tovarModal     = null;
var _aktifGuruh     = 0;

function tovarModalOch(btn) {
    _activeTovarRow = $(btn).closest('.tovar-qator');
    if (!_tovarModal) {
        _tovarModal = new bootstrap.Modal(document.getElementById('tovarIzlashModal'));
    }
    document.getElementById('tovar-modal-qidiruv').value = '';
    tovarGuruhFilter(0, document.querySelector('#tovar-guruh-tablar button[data-guruh="0"]'));
    _tovarModal.show();
    setTimeout(function() {
        document.getElementById('tovar-modal-qidiruv').focus();
    }, 400);
}

function tovarModalTanlash(tr) {
    if (!_activeTovarRow) return;
    _activeTovarRow.find('.tovar-nomi-inp').val(tr.dataset.nomi);
    _activeTovarRow.find('.tovar-narx').val(tr.dataset.narx).trigger('input');
    _activeTovarRow.find('.tovar-soni').val(1).trigger('input');
    tovarJamiHisoblash(_activeTovarRow.find('.tovar-soni')[0]);
    if (_tovarModal) _tovarModal.hide();
    setTimeout(function() {
        _activeTovarRow.find('.tovar-soni').focus();
    }, 200);
}

function tovarGuruhFilter(guruhId, btn) {
    _aktifGuruh = guruhId;
    document.querySelectorAll('#tovar-guruh-tablar button').forEach(function(b) {
        b.classList.remove('active');
    });
    if (btn) btn.classList.add('active');
    var q = document.getElementById('tovar-modal-qidiruv').value.toLowerCase().trim();
    tovarModalFiltr(q);
}

function tovarModalFiltr(q) {
    var visibleCount = 0;
    q = (q || '').toLowerCase().trim();
    document.querySelectorAll('.tovar-modal-qator').forEach(function(tr) {
        var nomi    = (tr.dataset.nomi || '').toLowerCase();
        var guruhOk = (_aktifGuruh === 0 || parseInt(tr.dataset.guruh) === _aktifGuruh);
        var qidirOk = (q === '' || nomi.includes(q));
        var show    = guruhOk && qidirOk;
        tr.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    var emptyEl = document.getElementById('tovar-modal-empty');
    if (emptyEl) emptyEl.classList.toggle('d-none', visibleCount > 0);
    var soniEl  = document.getElementById('tovar-modal-soni');
    if (soniEl) soniEl.textContent = visibleCount;
}

document.addEventListener('DOMContentLoaded', function() {
    var qEl = document.getElementById('tovar-modal-qidiruv');
    if (qEl) {
        qEl.addEventListener('input', function() { tovarModalFiltr(this.value); });
        qEl.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                var visible = Array.from(document.querySelectorAll('.tovar-modal-qator'))
                                   .filter(function(r) { return r.style.display !== 'none'; });
                if (visible.length === 1) tovarModalTanlash(visible[0]);
            }
        });
    }
    var jami = document.querySelectorAll('.tovar-modal-qator').length;
    var el   = document.getElementById('tovar-modal-soni');
    if (el) el.textContent = jami;
});
</script>
@endpush
@endsection