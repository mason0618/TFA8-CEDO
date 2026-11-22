@extends('layouts.app')
@section('title', 'Profile')
@section('content')
    <section class="page-heading"><p class="eyebrow">Your account</p><h1>Profile settings</h1><p class="lead">Update your details and manage your password.</p></section>
    <div class="settings-grid">
        <section class="form-panel">
            <h2>Profile information</h2>
            @include('partials.form-errors')
            <form method="POST" action="{{ route('profile.update') }}" novalidate>
                @csrf @method('PATCH')
                <div class="form-field"><label for="full_name">Full name</label><input id="full_name" name="full_name" value="{{ old('full_name', $user->full_name) }}" autocomplete="name" required></div>
                <div class="form-field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" autocomplete="email" required></div>
                <button class="button" type="submit">Save profile</button>
            </form>
        </section>
        <section class="form-panel" id="password-settings">
            <h2>Update password</h2>
            @if ($errors->updatePassword->any())
                <div class="error-summary" role="alert"><ul>@foreach ($errors->updatePassword->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ route('password.update') }}" novalidate>
                @csrf @method('PUT')
                <div class="form-field"><label for="current_password">Current password</label><input id="current_password" name="current_password" type="password" autocomplete="current-password" required></div>
                <div class="form-field"><label for="new_password">New password</label><input id="new_password" name="password" type="password" autocomplete="new-password" required></div>
                <div class="form-field"><label for="confirm_new_password">Confirm new password</label><input id="confirm_new_password" name="password_confirmation" type="password" autocomplete="new-password" required></div>
                <button class="button" type="submit">Update password</button>
            </form>
        </section>
    </div>
    <section class="form-panel danger-zone">
        <h2>Delete your account</h2>
        <p>This permanently removes your user account and profile picture. Your current password is required.</p>
        @if ($errors->userDeletion->any())
            <div class="error-summary" role="alert"><ul>@foreach ($errors->userDeletion->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <details @if($errors->userDeletion->any()) open @endif>
            <summary>Show account deletion form</summary>
            <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Permanently delete your own account?');" novalidate>
                @csrf @method('DELETE')
                <div class="form-field"><label for="delete_password">Current password</label><input id="delete_password" name="password" type="password" autocomplete="current-password" required></div>
                <button class="button button-danger" type="submit">Delete my account</button>
            </form>
        </details>
    </section>
@endsection
