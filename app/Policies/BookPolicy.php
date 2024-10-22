<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\UserRoleEnum;
use App\Models\Book;

class BookPolicy
{
    /**
     * Determine if the user can create a book.
     *
     * This policy allows SuperAdmin and Admin roles to create books.
     *
     * @param  User  $user
     * @return boolean
     */
    public function create(User $user)
    {
        return in_array($user->role, [
            UserRoleEnum::SUPERADMIN->value,
            UserRoleEnum::ADMIN->value
        ]);
    }

    /**
     * Determine if the user can update a book.
     *
     * @param  User  $user The user attempting to update the book.
     * @param  Book  $book The book to be updated.
     * @return bool Whether the user has permission to update the book.
     *
     * A user can update a book if they are a super-admin or an admin who
     * created the book.
     */
    public function update(User $user, Book $book)
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

    /**
     * Determine if the user can delete a book.
     *
     * @param  User $user The user attempting to delete the book.
     * @param  Book $book The book to be deleted.
     * @return bool Whether the user has permission to delete the book.
     */
    public function delete(User $user, Book $book)
    {
        return $user->role === UserRoleEnum::SUPERADMIN->value ||
            ($user->role === UserRoleEnum::ADMIN->value && $user->id === $book->admin_id);
    }
}
