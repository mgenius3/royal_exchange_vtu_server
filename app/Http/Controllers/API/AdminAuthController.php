<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller; // Import the base Controller class

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:admins',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $admin = Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $admin->createToken('admin_token', ['admin'])->plainTextToken;

        return response()->json(['admin' => $admin, 'token' => $token], 201);
    }

    public function login(Request $request)
    {

        // Log the request data
        // dump('Login Request Data:', $request->all());

        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('email', $credentials['email'])->first();
        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = $admin->createToken('admin_token', ['admin'])->plainTextToken;
        // Store the admin token in the session
        session(['admin_token' => $token]);
        return response()->json(['admin' => $admin, 'token' => $token], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
