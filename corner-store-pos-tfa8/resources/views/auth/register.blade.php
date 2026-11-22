@extends('layouts.app')
@section('title', 'Register')
@section('content')
    <div class="auth-shell">
        <section class="page-heading"><p class="eyebrow">Corner Store POS</p><h1>Register</h1><p class="lead">Create your account to access the store directory.</p></section>
        <section class="form-panel">
            @include('partials.form-errors')
            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf
                <div class="form-field"><label for="full_name">Full name</label><input id="full_name" type="text" name="full_name" value="{{ old('full_name') }}" maxlength="255" autocomplete="name" required autofocus></div>
                <div class="form-field"><label for="username">Username</label><input id="username" type="text" name="username" value="{{ old('username') }}" maxlength="255" autocomplete="username" required></div>
                <div class="form-field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></div>
                <div class="form-field"><label for="password">Password</label><input id="password" type="password" name="password" autocomplete="new-password" required></div><div class="form-field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
                <p class="form-help">Use a password with at least 8 characters.</p>
                <div class="form-actions"><button class="button" type="submit">Register</button><a class="text-link" href="{{ route('login') }}">Already registered?</a></div>
            </form>
        </section>
    </div>
@endsection
