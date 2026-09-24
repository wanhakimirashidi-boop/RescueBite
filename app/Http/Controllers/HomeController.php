<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    // for guest users, NOT consumer
    public function index()
    {
        return view('welcome'); //welcome.blade.php
    }
}
