<?php

namespace App\Policies;

use App\Enums\UserRoleEnum;
use App\Models\Book;
use App\Models\User;

class BorrowedBookPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user, Book $book)
    {
        if ($user->role === UserRoleEnum::SUPERADMIN->value) {
            return true;
        }

        // Admin can only update their own books
        if ($user->role === UserRoleEnum::ADMIN->value && $user->id === $book->admin_id) {
            return true;
        }

        return false;
    }
}
