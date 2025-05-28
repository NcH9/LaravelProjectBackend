<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function createUser(array $data):User {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }
    public function generateAccessToken($user)
    {
        $atExpireTime = now()->addMinutes(config('sanctum.expiration'));
        $accessToken = $user->createToken('access_token', ['access-api'], $atExpireTime);

        return $accessToken->plainTextToken;
    }
    public function checkUserDiscount(User $user, int $discountId):void {
        if (!$discountId || !$user->discounts()->contains($discountId)) {
            abort(401, 'User '.$user->name.' does not have #'.$discountId.' discount');
        }
    }
}
