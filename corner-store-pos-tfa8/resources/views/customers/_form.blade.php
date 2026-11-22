<div class="form-field">
    <label for="full_name">Full name <span class="required">*</span></label>
    <input id="full_name" name="full_name" type="text" maxlength="255" autocomplete="name" required value="{{ old('full_name', $customer->full_name ?? '') }}" @error('full_name') aria-invalid="true" aria-describedby="full_name-error" @enderror>
    @error('full_name') <p id="full_name-error" class="field-error">{{ $message }}</p> @enderror
</div>
<div class="form-field">
    <label for="email">Email address <span class="required">*</span></label>
    <input id="email" name="email" type="email" maxlength="255" autocomplete="email" required value="{{ old('email', $customer->email ?? '') }}" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror>
    @error('email') <p id="email-error" class="field-error">{{ $message }}</p> @enderror
</div>
<div class="form-field">
    <label for="phone">Phone number <span class="optional">(optional)</span></label>
    <input id="phone" name="phone" type="tel" maxlength="30" autocomplete="tel" value="{{ old('phone', $customer->phone ?? '') }}" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
    @error('phone') <p id="phone-error" class="field-error">{{ $message }}</p> @enderror
</div>
