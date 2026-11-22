<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;

class PageController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function about()
    {
        return view('about');
    }

    public function dashboard()
    {
        $customerCount = Customer::count();
        $userCount = User::count();

        return view('dashboard', compact('customerCount', 'userCount'));
    }
}
