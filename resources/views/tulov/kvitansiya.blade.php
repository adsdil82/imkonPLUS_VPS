<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kvitansiya #{{ $tulov->kvitansiya_raqam ?? ('KO-' . str_pad($tulov->id, 6, '0', STR_PAD_LEFT)) }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; color: #000; background: #fff; }

@media screen {
    body { background: #d0d0d0; padding: 16px; }
    .page-wrap { max-width: 290mm; margin: 0 auto; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,.2); }
}
@media print {
    body { background: #fff; margin: 0; padding: 0; }
    .no-print { display: none !important; }
    @page { size: A4 landscape; margin: 7mm 8mm; }
    .page-wrap { width: 100%; box-shadow: none; }
}

/* ── Print bar ─────────────────────────────── */
.print-bar {
    background: #1a3a2a; color: #fff; padding: 10px 20px;
    display: flex; align-items: center; gap: 12px;
}
.btn-print {
    background: #2d6a4f; color: #fff; border: 2px solid #51cf66;
    padding: 7px 22px; border-radius: 6px; font-size: 11pt;
    cursor: pointer; font-weight: bold;
}
.btn-print:hover { background: #1a3a2a; }
.btn-back {
    background: transparent; color: #aaa; border: 1px solid #555;
    padding: 7px 16px; border-radius: 6px; font-size: 10pt;
    text-decoration: none; display: inline-block;
}

/* ── Ikkita ustun ───────────────────────────── */
.yarmi-wrap {
    display: flex;
}
.yarmi {
    width: 50%;
    padding: 8mm 7mm 6mm 7mm;
    position: relative;
}
.yarmi-kassir {
    border-right: 1.5px dashed #888;
}

/* ── Makas chizig'i ─────────────────────────── */
.makas {
    position: absolute; right: -11px; top: 50%;
    transform: translateY(-50%) rotate(90deg);
    font-size: 18pt; color: #aaa; z-index: 2;
}

/* ── Kompaniya ──────────────────────────────── */
.komp-nomi {
    text-align: center; font-size: 10.5pt; font-weight: bold;
    text-transform: uppercase; letter-spacing: .5px;
    border-bottom: 1.5px solid #000;
    padding-bottom: 3px; margin-bottom: 3px;
}
.komp-info {
    text-align: center; font-size: 7.5pt; color: #444; margin-bottom: 5px; line-height: 1.4;
}

/* ── Orden sarlavha ─────────────────────────── */
.orden-title {
    text-align: center; font-size: 11.5pt; font-weight: bold;
    text-transform: uppercase; letter-spacing: 1.5px; margin: 6px 0 1px;
}
.orden-no {
    text-align: center; font-size: 9pt; color: #555; margin-bottom: 7px;
}

/* ── Ma'lumotlar jadvali ────────────────────── */
.data-table { width: 100%; border-collapse: collapse; }
.data-table td { padding: 2.5px 2px; vertical-align: bottom; font-size: 9.5pt; }
.data-table .lbl { white-space: nowrap; color: #555; width: 1%; font-size: 8.5pt; }
.data-table .val {
    border-bottom: 1px solid #666; padding-left: 4px;
    padding-bottom: 1px; font-weight: bold;
}

/* ── Summa quticha ──────────────────────────── */
.summa-box {
    text-align: center; font-size: 13.5pt; font-weight: bold;
    border: 2px solid #000; padding: 5px 8px;
    margin: 7px 0 3px; letter-spacing: .5px;
}
.summa-yozuv {
    font-size: 8.5pt; color: #333; font-style: italic;
    border-bottom: 1px solid #bbb; padding-bottom: 2px; margin-bottom: 7px;
    line-height: 1.4;
}

/* ── Imzo qatorlari ─────────────────────────── */
.imzo-wrap {
    display: flex; gap: 6px; margin-top: 8px;
}
.imzo-blok { flex: 1; text-align: center; }
.imzo-blok .il { font-size: 7.5pt; color: #555; }
.imzo-blok .ic { border-top: 1px solid #555; margin: 18px 4px 2px; }
.imzo-blok .in { font-size: 7pt; }

/* ── Muhr ────────────────────────────────────── */
.muhr {
    float: right; width: 52px; height: 52px;
    border: 1px dashed #bbb; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 6.5pt; color: #bbb; text-align: center;
    margin: -22px 0 0 0;
}

/* ── Talon tepa yozuv ───────────────────────── */
.talon-head {
    text-align: center; font-size: 8pt; font-weight: bold;
    color: #666; border-bottom: 1px solid #aaa;
    padding-bottom: 3px; margin-bottom: 4px;
    text-transform: uppercase; letter-spacing: 1.5px;
}

/* ── Pastki izoh ────────────────────────────── */
.pastki {
    font-size: 6.5pt; color: #888; text-align: center;
    margin-top: 10px; border-top: 1px dotted #ccc; padding-top: 4px;
}
</style>
</head>
<body>

{{-- ── Chop etish paneli ─────────────────────────────── --}}
<div class="print-bar no-print">
    <button class="btn-print" onclick="window.print()">
        🖨️ &nbsp;Chop etish
    </button>
    <a class="btn-back" href="{{ route('kreditlar.show', $kredit) }}">← Orqaga</a>
    <span style="color:#888;font-size:9pt;margin-left:8px">
        A4 Landscape • Chap = Kassir nusxasi &nbsp;|&nbsp; O'ng = Mijoz taloni
    </span>
</div>

@php
$kvNo        = $tulov->kvitansiya_raqam ?? ('KO-' . str_pad($tulov->id, 6, '0', STR_PAD_LEFT));
$sana        = $tulov->tolov_sana ? $tulov->tolov_sana->format('d.m.Y') : now()->format('d.m.Y');
$summa       = number_format((float)$tulov->summa, 2, '.', ' ');
$summaInt    = number_format((float)$tulov->summa, 0, '.', ' ');
$mijozIsm    = ($kredit->mijoz->familiya ?? '') . ' ' . ($kredit->mijoz->ism ?? '');
$asoslar     = $kredit->shartnoma_raqam . ' shartnomasi bo\'yicha nasiya to\'lovi';
$kassir      = $tulov->xodim->ism_familiya ?? '';
$tulovTuri   = $tulov->tulovTuri->nomi ?? '';
$qoldiq      = number_format((float)$kredit->qoldiq_qarz, 0, '.', ' ');
$kreditSumma = number_format((float)$kredit->kredit_summa, 0, '.', ' ');

$kompNomi     = $soz['kompaniya_nomi']    ?? ($soz['brand_nomi'] ?? 'NasiyaPro');
$kompManzil   = $soz['kompaniya_manzil']  ?? '';
$kompTelefon  = $soz['kompaniya_telefon'] ?? '';
$kompINN      = $soz['kompaniya_inn']     ?? '';
$kompHisob    = $soz['kompaniya_hisob']   ?? '';
$kompBank     = $soz['kompaniya_bank']    ?? '';
$kompDirektor = $soz['kompaniya_direktor'] ?? '';
@endphp

<div class="page-wrap">
<div class="yarmi-wrap">

    {{-- ═══════════════════════════════════════════════════
         CHAP — KASSIR (Buxgalteriya) nusxasi
    ════════════════════════════════════════════════════ --}}
    <div class="yarmi yarmi-kassir">

        <div class="komp-nomi">{{ $kompNomi }}</div>
        <div class="komp-info">
            @if($kompManzil){{ $kompManzil }}@endif
            @if($kompTelefon) &nbsp;|&nbsp; {{ $kompTelefon }}@endif
            @if($kompINN)<br>INN: {{ $kompINN }} &nbsp;|&nbsp; Hisob: {{ $kompHisob }}@endif
            @if($kompBank)<br>{{ $kompBank }}@endif
        </div>

        <div class="orden-title">Kassa Kirim Orderi</div>
        <div class="orden-no">
            № &nbsp;<strong>{{ $kvNo }}</strong>
            &emsp;&emsp;
            {{ $sana }}
        </div>

        <table class="data-table">
            <tr>
                <td class="lbl">Qabul qilindi:</td>
                <td class="val">{{ $mijozIsm }}</td>
            </tr>
            <tr>
                <td class="lbl">Asosi:</td>
                <td class="val">{{ $asoslar }}</td>
            </tr>
            <tr>
                <td class="lbl">To'lov turi:</td>
                <td class="val">{{ $tulovTuri }}</td>
            </tr>
            @if($tulov->izoh)
            <tr>
                <td class="lbl">Izoh:</td>
                <td class="val">{{ $tulov->izoh }}</td>
            </tr>
            @endif
        </table>

        <div class="summa-box">{{ $summa }} so'm</div>
        <div class="summa-yozuv">
            Yozuvda: {{ ucfirst($summaSoz) }} 00 tiyin
        </div>

        <div style="overflow:hidden">
            <div class="muhr">Muhr<br>joyi</div>
            <div class="imzo-wrap">
                <div class="imzo-blok">
                    <div class="il">Kassir</div>
                    <div class="ic"></div>
                    <div class="in">{{ $kassir }}</div>
                </div>
                <div class="imzo-blok">
                    <div class="il">Direktor</div>
                    <div class="ic"></div>
                    <div class="in">{{ $kompDirektor }}</div>
                </div>
                <div class="imzo-blok">
                    <div class="il">Buxgalter</div>
                    <div class="ic"></div>
                    <div class="in">&nbsp;</div>
                </div>
            </div>
        </div>

        <div class="pastki">✂ &nbsp; Kassir nusxasi — Buxgalteriyada saqlanadi</div>

        <div class="makas no-print">✂</div>
    </div>

    {{-- ═══════════════════════════════════════════════════
         O'NG — MIJOZ taloni
    ════════════════════════════════════════════════════ --}}
    <div class="yarmi">

        <div class="talon-head">Mijozga beriladigan talon</div>

        <div class="komp-nomi">{{ $kompNomi }}</div>
        <div class="komp-info">
            @if($kompTelefon)Tel: {{ $kompTelefon }}@endif
        </div>

        <div class="orden-title" style="font-size:10.5pt">Kassa Kirim Orderi</div>
        <div class="orden-no">
            № &nbsp;<strong>{{ $kvNo }}</strong>
            &emsp;&emsp;
            {{ $sana }}
        </div>

        <table class="data-table">
            <tr>
                <td class="lbl">Mijoz:</td>
                <td class="val">{{ $mijozIsm }}</td>
            </tr>
            <tr>
                <td class="lbl">Shartnoma:</td>
                <td class="val">{{ $kredit->shartnoma_raqam }}</td>
            </tr>
            <tr>
                <td class="lbl">To'lov turi:</td>
                <td class="val">{{ $tulovTuri }}</td>
            </tr>
            <tr>
                <td class="lbl">Sana:</td>
                <td class="val">{{ $sana }}</td>
            </tr>
        </table>

        <div class="summa-box" style="font-size:16pt">{{ $summaInt }} so'm</div>
        <div class="summa-yozuv">
            {{ ucfirst($summaSoz) }} 00 tiyin
        </div>

        <table class="data-table" style="margin-top:6px">
            <tr>
                <td class="lbl">Qoldiq qarz:</td>
                <td class="val" style="color:#c62828;font-size:9pt">{{ $qoldiq }} so'm</td>
            </tr>
            <tr>
                <td class="lbl">Jami kredit:</td>
                <td class="val" style="font-size:9pt">{{ $kreditSumma }} so'm</td>
            </tr>
        </table>

        <div class="imzo-wrap" style="margin-top:14px">
            <div class="imzo-blok">
                <div class="il">Kassir imzosi</div>
                <div class="ic"></div>
                <div class="in">{{ $kassir }}</div>
            </div>
            <div class="imzo-blok" style="flex:2">
                <div class="il">Mijoz imzosi</div>
                <div class="ic"></div>
                <div class="in">&nbsp;</div>
            </div>
        </div>

        <div class="pastki">
            ✂ &nbsp; Mijoz nusxasi — Saqlang! &nbsp;|&nbsp; {{ $kompNomi }}
        </div>
    </div>

</div>
</div>

</body>
</html>
