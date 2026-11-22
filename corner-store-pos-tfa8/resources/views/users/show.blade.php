@extends('layouts.app')
@section('title', 'User Details')
@section('content')
    <section class="page-heading"><p class="eyebrow">User Accounts</p><h1>User Details</h1><p class="lead">{{ $user->full_name }}</p></section>
    <section class="form-panel">
        <img class="avatar avatar-preview" width="96" height="96" src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('images/avatar-placeholder.svg') }}" alt="Profile picture for {{ $user->full_name }}">
        <dl class="record-details"><div><dt>Full name</dt><dd>{{ $user->full_name }}</dd></div><div><dt>Username</dt><dd>{{ $user->username }}</dd></div><div><dt>Login email</dt><dd>{{ $user->email ?: 'Not set' }}</dd></div></dl>
        <div class="form-actions"><a class="button" href="{{ route('users.edit', $user) }}">Edit user</a><a class="button button-light" href="{{ route('users.index') }}">Back to list</a></div>
    </section>
@endsection
