<?php

namespace App\Http\Controllers;

class ConsumerController extends Controller
{
    // Dashboard page for consumers
    public function dashboard()
    {
        return view('consumer.dashboard');  // dashboard.blade.php
    }

    // My Rescues page - Active Rescues for consumers
    public function rescues()
    {
        return view('consumer.rescues'); // rescues.blade.php
    }

    // My Rescues History page - Past Rescues for consumers
    public function rescueHistory()
    {
        return view('consumer.rescues-history'); // rescues-history.blade.php
    }
}
