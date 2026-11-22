@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <section class="hero">
        <div class="hero-copy">
            <p class="eyebrow">Welcome to Corner Store</p>
            <h1>Your store accounts,<br>in one place.</h1>
            <p class="lead">A simple directory for the customers you serve and the people behind your counter.</p>
            <div class="button-group">
                <a class="button" href="{{ route('customers.index') }}">View customer accounts <span aria-hidden="true">&rarr;</span></a>
                <a class="button button-light" href="{{ route('users.index') }}">View user accounts</a>
            </div>
        </div>
        <aside class="welcome-note" aria-label="Getting started">
            <span class="note-label">THE ACCOUNT BOOK</span>
            <h2>A familiar place<br>to keep track.</h2>
            <p>Find customer contact details and staff accounts without digging through separate lists.</p>
            <a class="text-link" href="{{ route('about') }}">About this project <span aria-hidden="true">&rarr;</span></a>
        </aside>
    </section>

    <section class="directory-section" aria-labelledby="directory-heading">
        <p class="eyebrow">Take a look around</p>
        <h2 id="directory-heading">Start with an account list</h2>
        <div class="card-grid">
            <article class="directory-card">
                <span class="card-number" aria-hidden="true">01</span>
                <h3>Customer Accounts</h3>
                <p>Browse customer names, email addresses, and phone numbers.</p>
                <a class="text-link" href="{{ route('customers.index') }}">Open customer list <span aria-hidden="true">&rarr;</span></a>
            </article>
            <article class="directory-card">
                <span class="card-number" aria-hidden="true">02</span>
                <h3>User Accounts</h3>
                <p>Find the store team's full names and usernames in one directory.</p>
                <a class="text-link" href="{{ route('users.index') }}">Open user list <span aria-hidden="true">&rarr;</span></a>
            </article>
        </div>
    </section>
@endsection
