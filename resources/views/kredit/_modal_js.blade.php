@push("scripts")
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
            <input type="hidden" name="tovarlar[${i}][tovar_katalog_id]" class="tovar-katalog-id" value="">
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
// ── Mijoz modal draggable ───────────────────────────────────────
(function() {
    var el = document.getElementById('mijozIzlashModal');
    if (!el) return;
    el.addEventListener('shown.bs.modal', function() {
        var dialog = document.getElementById('mijozModalDialog');
        var header = document.getElementById('mijozModalHeader');
        if (!dialog || !header) return;
        var vw = window.innerWidth, vh = window.innerHeight;
        dialog.style.position = 'fixed';
        dialog.style.left = Math.max(0, (vw - dialog.offsetWidth) / 2) + 'px';
        dialog.style.top  = Math.max(0, vh * 0.08) + 'px';
        dialog.style.margin = '0';
    });
    el.addEventListener('hidden.bs.modal', function() {
        var d = document.getElementById('mijozModalDialog');
        if (d) { d.style.position=''; d.style.left=''; d.style.top=''; d.style.margin=''; }
    });
    document.addEventListener('mousedown', function(e) {
        var header = document.getElementById('mijozModalHeader');
        if (!header || !header.contains(e.target)) return;
        if (e.target.closest('button, a')) return;
        var dialog = document.getElementById('mijozModalDialog');
        if (!dialog || dialog.style.position !== 'fixed') return;
        e.preventDefault();
        var sx = e.clientX - dialog.offsetLeft;
        var sy = e.clientY - dialog.offsetTop;
        function mv(ev) {
            dialog.style.left = Math.max(0, Math.min(window.innerWidth  - dialog.offsetWidth,  ev.clientX - sx)) + 'px';
            dialog.style.top  = Math.max(0, Math.min(window.innerHeight - dialog.offsetHeight, ev.clientY - sy)) + 'px';
        }
        function up() { document.removeEventListener('mousemove', mv); document.removeEventListener('mouseup', up); }
        document.addEventListener('mousemove', mv);
        document.addEventListener('mouseup', up);
    });
})();

var _mijozModal = null;
var _mijozTimer = null;

function mijozModalOch() {
    if (!_mijozModal) {
        _mijozModal = new bootstrap.Modal(document.getElementById('mijozIzlashModal'));
    }
    document.getElementById('mijoz-modal-qidiruv').value = '';
    document.getElementById('mijoz-modal-jadval').classList.add('d-none');
    document.getElementById('mijoz-modal-empty').classList.add('d-none');
    document.getElementById('mijoz-modal-hint').classList.add('d-none');
    document.getElementById('mijoz-modal-tbody').innerHTML = '';
    document.getElementById('mijoz-modal-spinner').classList.remove('d-none');
    _mijozModal.show();
    // Barcha mijozlarni avtomatik yuklash
    setTimeout(function() {
        mijozQidirAjax('');
        document.getElementById('mijoz-modal-qidiruv').focus();
    }, 300);
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
        document.getElementById('mijoz-modal-spinner').classList.remove('d-none');
        document.getElementById('mijoz-modal-hint').classList.add('d-none');
        _mijozTimer = setTimeout(function() { mijozQidirAjax(q); }, 250);
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
            document.getElementById('mijoz-modal-spinner').classList.remove('d-none');
            mijozQidirAjax(q);
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
                if (empty.querySelector('div')) empty.querySelector('div').textContent = 'Mijoz topilmadi';
                tbody.innerHTML = '';
                return;
            }
            empty.classList.add('d-none');
            jadval.classList.remove('d-none');
            // Sarlavhaga soni ko'rsatish
            var hdr = document.getElementById('mijoz-modal-soni-hdr');
            if (hdr) hdr.textContent = data.length + (data.length >= 50 ? ' ta (birinchi 50 ta)' : ' ta topildi');

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
        .fail(function(xhr, status, err) {
            document.getElementById('mijoz-modal-spinner').classList.add('d-none');
            document.getElementById('mijoz-modal-hint').classList.add('d-none');
            var empty = document.getElementById('mijoz-modal-empty');
            empty.classList.remove('d-none');
            empty.querySelector('div').textContent = 'Xatolik yuz berdi. Sahifani yangilang.';
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
    // Avvalgi highlight larni tozalash
    document.querySelectorAll('.tovar-modal-qator').forEach(function(r){ r.classList.remove('table-success'); });
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
    _activeTovarRow.find('.tovar-katalog-id').val(tr.dataset.id);
    tovarJamiHisoblash(_activeTovarRow.find('.tovar-soni')[0]);
    // Tanlangan qatorni highlight qilish
    document.querySelectorAll('.tovar-modal-qator').forEach(function(r){ r.classList.remove('table-success'); });
    tr.classList.add('table-success');
    if (_tovarModal) _tovarModal.hide();
