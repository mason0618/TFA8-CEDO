@extends('layouts.app')
@section('title', 'Verify email')
@section('content')
    <div class="auth-shell">
        <section class="page-heading"><p class="eyebrow">Corner Store POS</p><h1>Verify email</h1><p class="lead">Use the verification link sent to your account email.</p></section>
        <section class="form-panel">
            @include('partials.form-errors')
            <form method="POST" action="{{ route('verification.send') }}">@csrf<button class="button" type="submit">Resend verification email</button></form>
            <form method="POST" action="{{ route('logout') }}" class="auth-note">@csrf<button class="link-button" type="submit">Log out</button></form>
        </section>
    </div>
@endsection
