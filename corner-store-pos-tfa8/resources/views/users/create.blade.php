@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <section class="page-heading">
        <p class="eyebrow">User Accounts</p>
        <h1>Create User</h1>
        <p class="lead">Add a new user to the store directory.</p>
    </section>

    <section class="form-panel">
        @include('partials.form-errors')
        <form action="{{ route('users.store') }}" method="POST" novalidate>
            @csrf
            @include('users._form')
            <p class="form-help">A profile picture can be added from the edit page after creating the account.</p>
            <div class="form-actions">
                <button class="button" type="submit">Save user</button>
                <a class="button button-light" href="{{ route('users.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
