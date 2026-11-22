@extends('layouts.app')
@section('title', 'Customer Details')
@section('content')
    <section class="page-heading"><p class="eyebrow">Customer Accounts</p><h1>Customer Details</h1><p class="lead">{{ $customer->full_name }}</p></section>
    <section class="form-panel">
        <dl class="record-details"><div><dt>Full name</dt><dd>{{ $customer->full_name }}</dd></div><div><dt>Email address</dt><dd>{{ $customer->email }}</dd></div><div><dt>Phone number</dt><dd>{{ $customer->phone ?: '—' }}</dd></div></dl>
        <div class="form-actions"><a class="button" href="{{ route('customers.edit', $customer) }}">Edit customer</a><a class="button button-light" href="{{ route('customers.index') }}">Back to list</a></div>
    </section>
@endsection
