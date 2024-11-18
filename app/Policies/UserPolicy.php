<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\User;

class UserPolicy
{
    public function accessAllRoutes(User $user)
    {
        return $user->role === UserRoleEnum::SUPERADMIN->value;
    }

    /**
     * Determine if the logged-in user can only read the list of users (Admin).
     */
    public function readUserList(User $user)
    {
        return in_array($user->role, [UserRoleEnum::SUPERADMIN->value, UserRoleEnum::ADMIN->value]);
    }
}
