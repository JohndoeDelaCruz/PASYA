<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerController extends Controller
{
    /**
     * Display the farmer dashboard.
     */
    public function dashboard()
    {
        $user = Auth::guard('farmer')->user();
        return view('farmers.dashboard', compact('user'));
    }

    /**
     * Display the farmer profile.
     */
    public function profile()
    {
        $user = Auth::guard('farmer')->user();
        return view('farmers.profile', compact('user'));
    }

    /**
     * Display the farmer calendar.
     */
    public function calendar()
    {
        $user = Auth::guard('farmer')->user();
        return view('farmers.calendar', compact('user'));
    }

    /**
     * Display the harvest history.
     */
    public function harvestHistory()
    {
        $user = Auth::guard('farmer')->user();
        return view('farmers.harvest-history', compact('user'));
    }

    /**
     * Display the price list watch.
     */
    public function pricelistWatch()
    {
        $user = Auth::guard('farmer')->user();
        return view('farmers.pricelist-watch', compact('user'));
    }
}