<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('id')->get();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(CustomerRequest $request)
    {
        $data = $request->validated();
        $data['phone'] = $data['phone'] ?? '';
        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Customer account created.');
    }

    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();
        $data['phone'] = $data['phone'] ?? '';
        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Customer account updated.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer account deleted.');
    }
}
