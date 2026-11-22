@extends('layouts.app')

@section('title', 'Customer Accounts')

@section('content')
    <section class="page-heading heading-actions">
        <div>
            <p class="eyebrow">The people we serve</p>
        <h1>Customer Accounts</h1>
        <p class="lead">Customer names and contact details at a glance.</p>
        </div>
        <a class="button" href="{{ route('customers.create') }}">Create Customer</a>
    </section>

    <section class="table-panel" aria-labelledby="customer-list-heading">
        <div class="table-heading">
            <h2 id="customer-list-heading">Customer directory</h2>
            <span class="record-count">{{ $customers->count() }} records</span>
        </div>
        <div class="table-scroll" tabindex="0" role="region" aria-label="Customer directory table, scroll horizontally on small screens">
            <table>
                <caption class="sr-only">Customer accounts and contact information</caption>
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Full name</th>
                        <th scope="col">Email address</th>
                        <th scope="col">Phone number</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr>
                            <td class="account-id">C{{ str_pad($customer->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <th scope="row">{{ $customer->full_name }}</th>
                            <td>{{ $customer->email }}</td>
                            <td class="nowrap">{{ $customer->phone ?: '—' }}</td>
                            <td><div class="row-actions">
                                <a class="text-link" href="{{ route('customers.show', $customer) }}">View</a>
                                <a class="text-link" href="{{ route('customers.edit', $customer) }}" aria-label="Edit {{ $customer->full_name }}">Edit</a>
                                
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Delete this customer account? This cannot be undone.');">
                                    @csrf @method('DELETE')
                                    <button class="link-button danger-link" type="submit">Delete</button>
                                </form>
                                
                            </div></td>
                        </tr>
                    @endforeach

                    @if ($customers->isEmpty())
                        <tr><td colspan="5">No customer accounts found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
        <p class="table-note">Customer accounts saved in the store directory.</p>
    </section>
@endsection
