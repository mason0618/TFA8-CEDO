@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
    <section class="page-heading">
        <p class="eyebrow">Customer Accounts</p>
        <h1>Edit Customer</h1>
        <p class="lead">Update the details for {{ $customer->full_name }}.</p>
    </section>

    <section class="form-panel">
        @include('partials.form-errors')
        <form action="{{ route('customers.update', $customer) }}" method="POST" novalidate>
            @csrf
            @method('PUT')
            @include('customers._form')
            <div class="form-actions">
                <button class="button" type="submit">Update customer</button>
                <a class="button button-light" href="{{ route('customers.index') }}">Cancel</a>
            </div>
        </form>
    </section>
@endsection
