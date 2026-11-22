@extends('layouts.app')

@section('title', 'About')

@section('content')
    <section class="page-heading">
        <p class="eyebrow">A little background</p>
        <h1>About Corner Store POS</h1>
        <p class="lead">A small project built around a store's everyday account records.</p>
    </section>

    <div class="about-grid">
        <article class="about-copy">
            <h2>Starting with the basics</h2>
            <p>Corner Store POS is a point of sale foundation project for IT0049, Technical Formative Assessment 8. It builds on TFA7 with resource controllers and login and registration through Laravel Breeze.</p>
            <p>The customer list contains full names, email addresses, and phone numbers. The user list shows the store team's full names and unique usernames.</p>
            <p>Only signed-in users can manage customer and user accounts. Profile pictures are still prepared as square thumbnails. Sales and inventory can be added in a future version.</p>
            <a class="button" href="{{ route('customers.index') }}">Browse the customer list <span aria-hidden="true">&rarr;</span></a>
        </article>
        <aside class="project-details" aria-labelledby="project-heading">
            <h2 id="project-heading">Project details</h2>
            <dl>
                <div><dt>Course</dt><dd>IT0049 &mdash; Web System Technologies</dd></div>
                <div><dt>Activity</dt><dd>TFA8 &mdash; Resource Controllers and Authentication</dd></div>
                <div><dt>Pages</dt><dd>Home, About, Customer Accounts, User Accounts</dd></div>
                <div><dt>Purpose</dt><dd>A starting point for a store account directory</dd></div>
            </dl>
        </aside>
    </div>
@endsection
