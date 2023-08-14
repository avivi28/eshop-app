<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login
     *    
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validate = validator($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->error($validate->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->error('User not found', 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->error('Password not match', 401);
        }

        $token = $user->createToken('api-token');


        return response()->api([
            'token' => $token->plainTextToken,
            'user' => $user,
        ]);
    }
}
