@php
    $isEdit = isset($user);
    $userRoles = $userRoles ?? []; // Ensure $userRoles is defined, default to empty array for create
@endphp

<div class="mb-3">
    <label for="name" class="form-label">{{ __('Nama') }}</label>
    <input type="text" class="form-control rounded-md @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $isEdit ? $user->name : '') }}" required autofocus>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">{{ __('Email') }}</label>
    <input type="email" class="form-control rounded-md @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $isEdit ? $user->email : '') }}" required>
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="password" class="form-label">{{ __('Kata Sandi') }} @if($isEdit) ({{ __('Kosongkan untuk tidak mengubah') }}) @endif</label>
    <input type="password" class="form-control rounded-md @error('password') is-invalid @enderror" id="password" name="password" @if(!$isEdit) required @endif>
    @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="password_confirmation" class="form-label">{{ __('Konfirmasi Kata Sandi') }}</label>
    <input type="password" class="form-control rounded-md" id="password_confirmation" name="password_confirmation" @if(!$isEdit) required @endif>
</div>

<div class="mb-3">
    <label for="business_location_id" class="form-label">{{ __('Lokasi') }}</label>
    <select class="form-select rounded-md @error('business_location_id') is-invalid @enderror" id="business_location_id" name="business_location_id">
        <option value="">-- {{ __('Pilih Lokasi') }} --</option>
        @foreach ($locations as $location)
            <option value="{{ $location->id }}"
                {{ old('business_location_id', $user->business_location_id ?? null) == $location->id ? 'selected' : '' }}
            >
                {{ $location->name }} ({{"{$location->name} ({$location->area} - {$location->city})"}})
            </option>
        @endforeach
    </select>
    @error('business_location_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-4">
    <label class="form-label">{{ __('Berikan Role') }}</label>
    <div class="row">
        @foreach ($roles as $role)
        <div class="col-md-4 col-sm-6 mb-2">
            <div class="form-check form-switch">
                <input class="form-check-input" type="radio" id="role_{{ $role->id }}" name="roles" value="{{ $role->name }}"
                    {{ in_array($role->name, (array) old('roles', $userRoles)) ? 'checked' : '' }}>
                <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
            </div>
        </div>
        @endforeach
    </div>
    @error('roles')
        <div class="text-danger mt-2">{{ $message }}</div>
    @enderror
</div>
