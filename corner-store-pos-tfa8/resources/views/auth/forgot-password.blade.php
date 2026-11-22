@extends('layouts.app')
@section('title', 'Forgot password')
@section('content')
    <div class="auth-shell">
        <section class="page-heading"><p class="eyebrow">Corner Store POS</p><h1>Forgot password</h1><p class="lead">Enter your account email to request a password reset link.</p></section>
        <section class="form-panel">
            @include('partials.form-errors')
            <form method="POST" action="{{ route('password.email') }}" novalidate>
                @csrf
                <div class="form-field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus></div>
                <div class="form-actions"><button class="button" type="submit">Email reset link</button><a class="text-link" href="{{ route('login') }}">Back to login</a></div>
            </form>
        </section>
    </div>
@endsection
