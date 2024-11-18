<?php

namespace App\Repositories;

use App\Models\BorrowedBook;

class BorrowedBookRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new BorrowedBook;
    }

    public function getUserBorrowedBooks($userId)
    {
        return $this->model->where('user_id', $userId)->with('book')->get();
    }

    public function getUsersByBook($bookId)
    {
        return $this->model->where('book_id', $bookId)->with('user')->get();
    }

    public function findByUuidAndUser($uuid, $userId)
    {
        return BorrowedBook::where('uuid_column', $uuid)->where('user_id', $userId)->first();
    }

    public function getOverdueBooks($currentDate)
    {
        return BorrowedBook::where('due_date', '<', $currentDate)
            ->with(['user', 'book'])
            ->get();
    }

    public function getUserReturnHistory($userId)
    {
        return BorrowedBook::withTrashed()->where('user_id', $userId)->where('deleted_at', '!=', null)->with('book')->get();
    }
}
