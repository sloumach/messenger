<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{

    public function __invoke(Request $request)
        {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            $user = Auth::user();
            if ($request->filled('fcm_token')) {
                $user->fcm_token = $request->fcm_token;
                $user->save();
            }
            if (!$user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Verify your email'], 403);
            }

            $token = $user->createToken('mobile')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
                'id' => $user->id,
            ]);
        }
}

