@extends('layouts.app')
@section('title', 'Log in')
@section('content')
    <div class="auth-shell">
        <section class="page-heading"><p class="eyebrow">Corner Store POS</p><h1>Log in</h1><p class="lead">Sign in to manage customer and user accounts.</p></section>
        <section class="form-panel">
            @include('partials.form-errors')
            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf
                <div class="form-field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></div>
                <div class="form-field"><label for="password">Password</label><input id="password" type="password" name="password" autocomplete="current-password" required></div>
                <label class="form-choice"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Remember me</label>
                <div class="form-actions"><button class="button" type="submit">Log in</button><a class="text-link" href="{{ route('password.request') }}">Forgot password?</a></div>
            </form>
            <p class="auth-note">New here? <a href="{{ route('register') }}">Create an account</a>.</p>
        </section>
    </div>
@endsection
