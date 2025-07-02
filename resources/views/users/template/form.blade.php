@php
    $isEdit = isset($user);
    $userRoles = $userRoles ?? []; // Ensure $userRoles is defined, default to empty array for create
@endphp

<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" class="form-control rounded-md @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $isEdit ? $user->name : '') }}" required autofocus>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email address</label>
    <input type="email" class="form-control rounded-md @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $isEdit ? $user->email : '') }}" required>
    @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="password" class="form-label">Password @if($isEdit)(leave blank to keep current)@endif</label>
    <input type="password" class="form-control rounded-md @error('password') is-invalid @enderror" id="password" name="password" @if(!$isEdit) required @endif>
    @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="password_confirmation" class="form-label">Confirm Password</label>
    <input type="password" class="form-control rounded-md" id="password_confirmation" name="password_confirmation" @if(!$isEdit) required @endif>
</div>

<div class="mb-4">
    <label class="form-label">Assign Roles</label>
    <div class="row">
        @foreach ($roles as $role)
        <div class="col-md-4 col-sm-6 mb-2">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="role_{{ $role->id }}" name="roles[]" value="{{ $role->name }}"
                    {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }}>
                <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
            </div>
        </div>
        @endforeach
    </div>
    @error('roles')
        <div class="text-danger mt-2">{{ $message }}</div>
    @enderror
</div>
