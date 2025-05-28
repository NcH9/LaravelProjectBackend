<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}
    public function index() {
        if (!Auth::check()) {

            return redirect()->route('login');
        }
        $user = User::find(auth()->user()->id);

        return isset($user) ? view('auth.profile')->with('user', $user) : response()->json('', 404);
    }
    public function register(RegisterRequest $request):JsonResponse {
        $data = $request->validated();

        $user = $this->authService->createUser($data);

        $user->assignRole('user');

        $token = $this->authService->generateAccessToken($user);
        $cookie = cookie('authToken', $token, config('sanctum.expiration'), secure: true, httpOnly: true, sameSite: 'None');

        return response()->json([
            'token' => $token
        ], 201)->withCookie($cookie);
    }
    public function checkToken(Request $request):JsonResponse {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }
        $tokenRecord = PersonalAccessToken::findToken($token);

        if ($tokenRecord === null || $tokenRecord->expires_at?->isPast()) {

            return response()->json(['error' => 'Token expired'], 401);
        }

        return response()->json([
            'accessToken' => 'success',
            'id' => $tokenRecord?->tokenable?->id,
        ]);
    }
    public function login(LoginRequest $request):JsonResponse {
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credentials do not match'
            ], 422);
        }

        $user = auth()->user();
        $token = $this->authService->generateAccessToken($user);
        $cookie = cookie('authToken', $token, config('sanctum.expiration'), secure: true, httpOnly: true, sameSite: 'None');
        return response()->json([
            'token' => $token,
        ], 201)->withCookie($cookie);
    }

}
