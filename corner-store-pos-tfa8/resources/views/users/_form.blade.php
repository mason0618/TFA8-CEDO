<div class="form-field">
    <label for="username">Username <span class="required">*</span></label>
    <input id="username" name="username" type="text" maxlength="255" autocomplete="username" required value="{{ old('username', $user->username ?? '') }}" @error('username') aria-invalid="true" aria-describedby="username-error" @enderror>
    @error('username') <p id="username-error" class="field-error">{{ $message }}</p> @enderror
</div>
<div class="form-field">
    <label for="full_name">Full name <span class="required">*</span></label>
    <input id="full_name" name="full_name" type="text" maxlength="255" autocomplete="name" required value="{{ old('full_name', $user->full_name ?? '') }}" @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
    @error('full_name') <p id="full_name-error" class="field-error">{{ $message }}</p> @enderror
</div>

<div class="form-field">
    <label for="email">Login email <span class="required">*</span></label>
    <input id="email" name="email" type="email" maxlength="255" autocomplete="email" required value="{{ old('email', $user->email ?? '') }}">
    @error('email') <p class="field-error">{{ $message }}</p> @enderror
</div>
@if (!isset($user) || !$user->password)
    @include('users._password-fields')
@elseif ($user->is(auth()->user()))
    <p class="form-help">Change your password from your <a href="{{ route('profile.edit') }}#password-settings">Profile settings</a>.</p>
@endif
