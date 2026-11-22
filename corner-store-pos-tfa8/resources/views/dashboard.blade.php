@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
    <section class="page-heading">
        <p class="eyebrow">Your workspace</p>
        <h1>Welcome, {{ auth()->user()->full_name }}.</h1>
        <p class="lead">You are signed in and ready to manage the store's accounts.</p>
    </section>
    <div class="card-grid">
        <article class="directory-card"><span class="dashboard-count">{{ $customerCount }}</span><h2>Customer Accounts</h2><p>View, create, update, and delete customer records.</p><a class="text-link" href="{{ route('customers.index') }}">Open customer accounts &rarr;</a></article>
        <article class="directory-card"><span class="dashboard-count">{{ $userCount }}</span><h2>User Accounts</h2><p>Manage the store team and their account details.</p><a class="text-link" href="{{ route('users.index') }}">Open user accounts &rarr;</a></article>
    </div>
@endsection
