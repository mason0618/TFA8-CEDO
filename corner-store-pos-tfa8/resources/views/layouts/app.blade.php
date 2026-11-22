<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Corner Store POS account management.">
    <title>@yield('title', 'Home') | Corner Store POS</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <a class="skip-link" href="#main-content">Skip to content</a>
    <header class="site-header">
        <div class="container header-content">
            <a class="brand" href="{{ route('home') }}"><span class="brand-mark" aria-hidden="true">CS</span><span>Corner Store <strong>POS</strong></span></a>
            <nav aria-label="Main navigation">
                <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Home</a>
                <a href="{{ route('about') }}" @if(request()->routeIs('about')) aria-current="page" @endif>About</a>
                <a href="{{ route('customers.index') }}" @if(request()->routeIs('customers.*')) aria-current="page" @endif>Customer Accounts</a>
                <a href="{{ route('users.index') }}" @if(request()->routeIs('users.*')) aria-current="page" @endif>User Accounts</a>
            </nav>
        </div>
        <div class="container account-bar">
            @auth
                <span>Signed in as <strong>{{ auth()->user()->full_name }}</strong></span>
                <div class="account-links">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('profile.edit') }}">Profile</a>
                    <form action="{{ route('logout') }}" method="POST">@csrf<button class="link-button" type="submit">Log out</button></form>
                </div>
            @else
                <span>Sign in to manage the store's accounts.</span>
                <div class="account-links"><a href="{{ route('login') }}">Log in</a><a href="{{ route('register') }}">Register</a></div>
            @endauth
        </div>
    </header>

    <main id="main-content" class="container">
        @if (session('success'))
            <div class="flash-message" role="status">{{ session('success') }}</div>
        @endif
        @if (session('status'))
            <div class="flash-message" role="status">{{ ['profile-updated' => 'Profile updated.', 'password-updated' => 'Password updated.', 'verification-link-sent' => 'A new verification link has been sent.'][session('status')] ?? session('status') }}</div>
        @endif
        @yield('content')
    </main>

    <footer class="site-footer"><div class="container footer-content"><span>Corner Store POS</span><span>IT0049 &middot; TFA8</span></div></footer>
</body>
</html>
