@extends('layouts.app')
@section('title', 'Confirm password')
@section('content')
    <div class="auth-shell">
        <section class="page-heading"><p class="eyebrow">Corner Store POS</p><h1>Confirm password</h1><p class="lead">Confirm your password before continuing.</p></section>
        <section class="form-panel">
            @include('partials.form-errors')
            <form method="POST" action="{{ route('password.confirm') }}" novalidate>
                @csrf
                <div class="form-field"><label for="password">Password</label><input id="password" type="password" name="password" autocomplete="current-password" required></div>
                <div class="form-actions"><button class="button" type="submit">Confirm password</button></div>
            </form>
        </section>
    </div>
@endsection
