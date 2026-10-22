<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Login: email + password hari nam token ekak denawa
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $data['email'])->first();

        // User nathnam hari password waradi nam 401 denawa
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Email hari password hari na'], 401);
        }

        // Token eka (mobile app eka meka save karagannawa)
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    // Me wen login una user wa denawa (token eka hari nam witharai)
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // Logout: dan use karapu token eka delete karanawa
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}