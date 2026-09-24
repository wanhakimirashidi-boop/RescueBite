<?php

namespace App\Http\Controllers;

class ConsumerController extends Controller
{
    // Dashboard page for consumers
    public function dashboard()
    {
        return view('consumer.dashboard');
    }

    // My Rescues page - Active Rescues for consumers
    public function rescues()
    {
        return view('consumer.rescues');
    }

    // My Rescues History page - Past Rescues for consumers
    public function rescueHistory()
    {
        return view('consumer.rescues-history');
    }
}
