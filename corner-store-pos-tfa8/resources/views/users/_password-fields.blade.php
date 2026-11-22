<div class="form-field">
    <label for="password">Login password <span class="required">*</span></label>
    <input id="password" name="password" type="password" autocomplete="new-password" required>
    <p class="form-help">At least 8 characters. Passwords are never shown or redisplayed.</p>
    @error('password') <p class="field-error">{{ $message }}</p> @enderror
</div>
<div class="form-field">
    <label for="password_confirmation">Confirm password <span class="required">*</span></label>
    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
</div>
