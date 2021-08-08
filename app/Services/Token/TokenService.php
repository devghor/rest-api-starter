<?php


namespace App\Services\Token;


class TokenService
{
    public function deleteUserAccessToken($user)
    {
        return $user->token()->delete();
    }

    public function createUserAccessToken($user)
    {
        return $user ? $user->createToken('myToken')->accessToken : null;
    }

    public function getUserAccessToken($user)
    {
        return $user->token();
    }
}
