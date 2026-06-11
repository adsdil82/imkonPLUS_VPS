@extends('layouts.app')

@section('title', 'Kelayotgan to\'lovlar')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('hisobotlar.index') }}">Hisobotlar</a></li>
    <li class="breadcrumb-item active">Kelayotgan to'lovlar</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold">
        <i class="bi bi-calendar-check me-1"></i> Kelayotgan to'lovlar
        <span class="badge bg-secondary ms-1">{{ $tulovlar->count() }}</span>
    </h5>
    <small class="text-muted">
        Keyingi 7 kun ichida to'lanishi kerak bo'lgan to'lovlar
        ({{ now()->format('d.m.Y') }} — {{ now()->addDays(7)->format('d.m.Y') }})
    </small>
</div>

@if($tulovlar->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center text-muted py-5">
        <i class="bi bi-calendar-check fs-3 d-block mb-2"></i>
        Keyingi 7 kun ichida to'lov kutilmayapti
    </div>
</div>
@else

{{-- Kunlik guruhlash --}}
@php
    $guruhlangan = $tulovlar->groupBy(fn($t) => $t->tolov_sana?->format('Y-m-d'));
@endphp

@foreach($guruhlangan as $sana => $guruh)
<div class="mb-3">
    <div class="d-flex align-items-center gap-2 mb-2">
        <span class="badge bg-primary fs-6 px-3 py-2">
            {{ \Carbon\Carbon::parse($sana)->isoFormat('D MMMM, dddd') }}
        </span>
        <span class="text-muted small">
            {{ $guruh->count() }} ta to'lov —
            jami: <strong>{{ number_format($guruh->sum('tolov_summa'), 0, '.', ' ') }} so'm</strong>
        </span>
        @if(\Carbon\Carbon::parse($sana)->isToday())
            <span class="badge bg-warning text-dark">Bugun</span>
        @elseif(\Carbon\Carbon::parse($sana)->isTomorrow())
            <span class="badge bg-info text-dark">Ertaga</span>
        @endif
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Shartnoma</th>
                        <th>Mijoz</th>
                        @if(Auth::user()->isAdmin())<th>Filial</th>@endif
                        <th class="text-end">To'lov summasi</th>
                        <th class="text-end">Qoldiq</th>
                        <th>Oy tartib</th>
                        <th>Holat</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guruh as $g)
                    <tr>
                        <td>
                            @if($g->kredit)
                            <a href="{{ route('kreditlar.show', $g->kredit) }}" class="text-decoration-none fw-medium">
                                {{ $g->kredit->shartnoma_raqam }}
                            </a>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($g->kredit?->mijoz)
                            <a href="{{ route('mijozlar.show', $g->kredit->mijoz) }}" class="text-decoration-none">
                                {{ $g->kredit->mijoz->familiya }} {{ $g->kredit->mijoz->ism }}
                            </a>
                            <div class="text-muted small">{{ $g->kredit->mijoz->telefon }}</div>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        @if(Auth::user()->isAdmin())
                        <td>
                            <span class="badge bg-secondary">{{ $g->kredit?->filial?->kod ?? '—' }}</span>
                        </td>
                        @endif
                        <td class="text-end fw-medium">
                            {{ number_format($g->tolov_summa, 0, '.', ' ') }}
                        </td>
                        <td class="text-end text-danger">
                            {{ number_format($g->qoldiq_suma, 0, '.', ' ') }}
                        </td>
                        <td class="text-muted small">
                            {{ $g->oylik_tartib }}-oy
                        </td>
                        <td>
                            <span class="badge bg-{{ $g->holat_rangi }}">{{ $g->holat }}</span>
                        </td>
                        <td>
                            @if($g->kredit)
                            <a href="{{ route('kreditlar.tulov.create', $g->kredit) }}"
                               class="btn btn-sm btn-success py-0">
                                <i class="bi bi-cash"></i>
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

@endif
@endsection
