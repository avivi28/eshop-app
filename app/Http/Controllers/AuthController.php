<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $token = Str::random(60);
            $api_token = hash('sha256', $token);

            return response()->api(['token' => $api_token]);
        }

        return response()->error('Invalid credentials', 401);
    }
}
