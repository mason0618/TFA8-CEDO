@extends('layouts.app')

@section('title', 'Create Customer')

@section('content')
    <section class="page-heading">
        <p class="eyebrow">Customer Accounts</p>
        <h1>Create Customer</h1>
        <p class="lead">Add a new customer to the store directory.</p>
    </section>

    <section class="form-panel">
        @include('partials.form-errors')
        <form action="{{ route('customers.store') }}" method="POST" novalidate>
            @csrf
            @include('customers._form')
            <div class="form-actions">
                <button class="button" type="submit">Save customer</button>
                <a class="button button-light" href="{{ route('customers.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
