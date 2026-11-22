@extends('layouts.app')
@section('title', 'Reset password')
@section('content')
    <div class="auth-shell">
        <section class="page-heading"><p class="eyebrow">Corner Store POS</p><h1>Reset password</h1><p class="lead">Choose a new password for your account.</p></section>
        <section class="form-panel">
            @include('partials.form-errors')
            <form method="POST" action="{{ route('password.store') }}" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <div class="form-field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="email"></div>
                <div class="form-field"><label for="password">Password</label><input id="password" type="password" name="password" autocomplete="new-password" required></div><div class="form-field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
                <div class="form-actions"><button class="button" type="submit">Reset password</button></div>
            </form>
        </section>
    </div>
@endsection
