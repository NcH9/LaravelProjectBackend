<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function createUser(array $data):User {
        return User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password)
        ]);
    }

    public function generateAccessToken($user)
    {
        $atExpireTime = now()->addMinutes(config('sanctum.expiration'));
        $accessToken = $user->createToken('access_token', ['access-api'], $atExpireTime);

        return $accessToken->plainTextToken;
    }
}
