<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing');
    }

    public function contact(Request $request)
    {
        // Handle contact form submission (e.g., send email, save to database)
        // For now, just redirect back with a success message
        return redirect()->back()->with('sent-message', 'Your message has been sent. Thank you!');
    }

    public function privacy(){
        return view("privacy");
    }
}