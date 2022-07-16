<?php

namespace App\Services\User;

use App\Http\Resources\Role\RoleResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{

    /**
     * Generate username
     * @param $firstName
     * @param string $lastName
     * @return string
     */
    public function generateUserName($firstName, $lastName = '')
    {
        $userName = '';

        if ($firstName) {
            $userName .= strtolower(explode(" ", $firstName)[0]);
        }

        if ($lastName) {
            $userName .= '.' . strtolower(explode(" ", $lastName)[0]);
        }

        $countUser = User::whereRaw("user_name REGEXP '^{$userName}(\.[0-9]*)?$'")->count();

        if (($countUser + 1) > 1) {
            $suffix = $countUser + 1;
            $userName .= '.' . $suffix;
        }

        return $userName;
    }


    /**
     * @param $originalPassword
     * @return string
     */
    public function generatePassword($originalPassword)
    {
        return Hash::make($originalPassword);
    }

    /**
     * Get common user information
     *
     * @param User $user
     *
     * @return array
     */
    public function getUserInformation(User $user)
    {
        $data = [];

        if ($user) {
            $data["id"] = $user->id;
            $data["userName"] = $user->user_name ?? null;
            $data["firstName"] = $user->first_name;
            $data["lastName"] = $user->last_name;
            $data["email"] = $user->email;
            $data["role"] = new RoleResource($user->roles->first());
        }

        return $data;
    }
}
