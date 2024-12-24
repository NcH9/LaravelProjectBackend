<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Validator;

class AuthController extends Controller
{
    public function index() {
        if (!Auth::check()) {

            return redirect()->route('login');
        }
        $user = User::find(auth()->user()->id);
        
        return isset($user) ? view('auth.profile')->with('user', $user) : response()->json('', 404);
    }
    public function getCredentialsWithToken() {
        $user = Auth::user();

        if ($user) {
            return response()->json([
                'message' => 'User retrieved successfully',
                'user' => $user,
            ]);
        }

        return response()->json([
            'message' => 'Unauthorized',
        ], 401);
    }
    public function register(Request $request) {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'validation failed',
                'errors' => $validated->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole('user');

        return response()->json([
            'token' => $user->createToken('API Token')->plainTextToken
        ], 201);
    }

    public function login(Request $request) {
        $validated = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validated->fails()) {
            return response()->json([
                'message' => 'validation failed',
                'errors' => $validated->errors()
            ], 422);
        }

        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'credentials do not match'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'token' => $user->createToken('API Token')->plainTextToken
        ], 201);
    }
}
