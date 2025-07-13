@php
    $isEdit = isset($businessLocation);
@endphp

<div class="mb-3">
    <label for="name" class="form-label">{{ __('Nama Lokasi') }}</label>
    <input type="text" class="form-control rounded-md @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $isEdit ? $businessLocation->name : '') }}" required autofocus>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="code" class="form-label">{{ __('Kode') }}</label>
    <input type="text" class="form-control rounded-md @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $isEdit ? $businessLocation->code : '') }}" required>
    @error('code')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="city" class="form-label">{{ __('Kota') }}</label>
    <input type="text" class="form-control rounded-md @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $isEdit ? $businessLocation->code : '') }}" required>
    @error('city')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="area" class="form-label">{{ __('Area') }}</label>
    <input type="text" class="form-control rounded-md @error('area') is-invalid @enderror" id="area" name="area" value="{{ old('area', $isEdit ? $businessLocation->area : '') }}" required>
    @error('area')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="phone" class="form-label">{{ __('No Telepon') }}</label>
    <input type="text" class="form-control rounded-md @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $isEdit ? $businessLocation->phone : '') }}" required>
    @error('phone')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="phone" class="form-label">{{ __('Tipe') }}</label>
    <select class="form-select rounded-md @error('type') is-invalid @enderror" id="type" name="type" required>
        <option value="">-- Pilih Tipe --</option>
        <option value="warehouse" {{ old('type', $isEdit ? $businessLocation->type : '') === 'warehouse' ? 'selected' : '' }}>Gudang</option>
        <option value="store" {{ old('type', $isEdit ? $businessLocation->type : '') === 'store' ? 'selected' : '' }}>Toko</option>
        <option value="office" {{ old('type', $isEdit ? $businessLocation->type : '') === 'office' ? 'selected' : '' }}>Kantor</option>
    </select>
    @error('type')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

