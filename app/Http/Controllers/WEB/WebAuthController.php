<?php

namespace App\Http\Controllers\WEB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WebAuthController extends Controller
{
public function login(Request $request)
{
    // Validate the input manually to avoid automatic redirect
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    if ($validator->fails()) {
        return redirect()->route('user.login')
            ->with('error', $validator->errors()->first())
            ->withInput();
    }

    // Check credentials and log in
    $credentials = $request->only('email', 'password');
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = Auth::user();
        $token = $user->createToken('user_token')->plainTextToken;
       // Store token in session for the next request
       session(['token' => $token]); // Persistent in session
       return redirect()->route('users.index')
       ->with('token', $token); // Pass token to session
    }

    // If credentials are invalid, redirect back to login form with error
    return redirect()->route('user.login')
        ->with('error', 'The email or password you entered is incorrect.')
        ->withInput();
}


public function logout(Request $request)
    {
        // Log out of session (for web)
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Return JSON response for API consistency
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}