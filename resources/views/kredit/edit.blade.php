@extends('layouts.app')
@section('title', 'Shartnomani tahrirlash')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('kreditlar.index') }}">Shartnomalar</a></li>
    <li class="breadcrumb-item"><a href="{{ route('kreditlar.show', $kredit) }}">{{ $kredit->shartnoma_raqam }}</a></li>
    <li class="breadcrumb-item active">Tahrirlash</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-lg-7">

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header py-2 d-flex align-items-center gap-2">
        <i class="bi bi-pencil-square text-primary"></i>
        <h6 class="mb-0 fw-bold">{{ $kredit->shartnoma_raqam }} — Tahrirlash</h6>
        <span class="badge bg-{{ $kredit->holat_rangi }} ms-auto">{{ $kredit->holatNomi }}</span>
    </div>
    <div class="card-body">

        {{-- Mijoz info --}}
        <div class="alert alert-secondary py-2 mb-3">
            <i class="bi bi-person me-1"></i>
            <strong>{{ $kredit->mijoz->familiya }} {{ $kredit->mijoz->ism }}</strong>
            &nbsp;·&nbsp; {{ $kredit->mijoz->telefon }}
        </div>

        <form method="POST" action="{{ route('kreditlar.update', $kredit) }}">
            @csrf
            @method('PUT')

            {{-- Sabab (majburiy) --}}
            <div class="mb-3">
                <label class="form-label fw-medium">
                    O'zgartirish sababi <span class="text-danger">*</span>
                </label>
                <input type="text" name="sabab"
                       class="form-control @error('sabab') is-invalid @enderror"
                       value="{{ old('sabab') }}"
                       placeholder="Masalan: Mijoz telefoni o'zgardi, kafil qo'shildi..."
                       minlength="5" required>
                @error('sabab')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Holat --}}
            <div class="mb-3">
                <label class="form-label fw-medium">Holat</label>
                <select name="holat" class="form-select @error('holat') is-invalid @enderror">
                    <option value="faol"          {{ $kredit->holat === 'faol'          ? 'selected' : '' }}>Faol (aktiv)</option>
                    <option value="muddati_otgan" {{ $kredit->holat === 'muddati_otgan' ? 'selected' : '' }}>Muddati o'tgan</option>
                    <option value="muzlatilgan"   {{ $kredit->holat === 'muzlatilgan'   ? 'selected' : '' }}>Muzlatilgan</option>
                    <option value="yopilgan"      {{ $kredit->holat === 'yopilgan'      ? 'selected' : '' }}>Yopilgan (passiv)</option>
                </select>
                @error('holat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Kafil --}}
            <h6 class="fw-bold text-muted border-bottom pb-1 mt-4 mb-3">Kafil ma'lumotlari (ixtiyoriy)</h6>
            <div class="row g-3 mb-3">
                <div class="col-sm-12">
                    <label class="form-label">F.I.O.</label>
                    <input type="text" name="kafil_ism" class="form-control"
                           value="{{ old('kafil_ism', $kredit->kafil_ism) }}"
                           placeholder="Kafil ismi familiyasi">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Telefon</label>
                    <input type="text" name="kafil_telefon" class="form-control"
                           value="{{ old('kafil_telefon', $kredit->kafil_telefon) }}"
                           placeholder="+998 90 000 00 00">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Manzil</label>
                    <input type="text" name="kafil_manzil" class="form-control"
                           value="{{ old('kafil_manzil', $kredit->kafil_manzil) }}"
                           placeholder="Kafil manzili">
                </div>
            </div>

            {{-- Izoh --}}
            <div class="mb-4">
                <label class="form-label">Izoh / Eslatma</label>
                <textarea name="izoh" class="form-control" rows="2"
                          placeholder="Qo'shimcha ma'lumot...">{{ old('izoh', $kredit->izoh) }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>Saqlash
                </button>
                <a href="{{ route('kreditlar.show', $kredit) }}" class="btn btn-outline-secondary">
                    Bekor qilish
                </a>
            </div>
        </form>
    </div>
</div>

</div>
</div>
@endsection