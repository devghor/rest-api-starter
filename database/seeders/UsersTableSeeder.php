<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Enums\UserEnum;
use App\Models\Role;
use App\Models\User;
use App\Services\User\UserService;
use App\Values\RoleValue;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userService = new UserService();

        $users = [
            [
                'id' => 1,
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'user_name' => 'superadmin',
                'email' => 'admin@app.com',
                'email_verified_at' => Carbon::now()->toDateTimeString(),
                'password' => $userService->generatePassword("S123456"),
            ],

        ];

        $superAdminUser = User::find(UserEnum::SUPER_ADMIN_USER_ID) ?? User::create($users[0]);
        $saRole = Role::find(RoleEnum::SUPER_ADMIN_ROLE_ID);
        $superAdminUser->attachRole($saRole);
    }
}
