<?php

namespace App\Services;

use App\Enums\BookStatusEnum;
use App\Repositories\BorrowedBookRepository;
use Illuminate\Support\Facades\Auth;

class BorrowedBookService
{
    protected $borrowedBookRepository;

    public function __construct(BorrowedBookRepository $borrowedBookRepository)
    {
        $this->borrowedBookRepository = $borrowedBookRepository;
    }

    /**
     * Borrow a book by the given user.
     *
     * @param  \App\Models\Book  $book  The book to borrow.
     * @param  \App\Models\User  $user  The user to borrow the book.
     * @return \App\Models\BorrowedBook The created borrowed book.
     */
    public function borrowBook($book, $user)
    {
        $data = [
            'book_id' => $book->id,
            'user_id' => $user->id,
            'borrow_date' => now(),
            'due_date' => now()->addDays(10),
        ];
        $book->update(['status' => BookStatusEnum::NOT_AVAILABLE->value]);

        return $this->borrowedBookRepository->create($data);
    }

    /**
     * Retrieve all books borrowed by the current user.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of borrowed books.
     */
    public function getUserBorrowedBooks()
    {
        $user = Auth::user();

        return $this->borrowedBookRepository->getUserBorrowedBooks($user->id);
    }

    /**
     * Retrieve users who have borrowed a specific book.
     *
     * @param  \App\Models\Book  $book  The book to check.
     * @return \Illuminate\Database\Eloquent\Collection The collection of users who have borrowed the book.
     */
    public function getUsersByBook($book)
    {
        return $this->borrowedBookRepository->getUsersByBook($book->id);
    }

    /**
     * Retrieve a borrowed book by its UUID.
     *
     * @param  string  $uuid  The UUID of the borrowed book to retrieve.
     * @return \App\Models\BorrowedBook|null The borrowed book if found, null otherwise.
     */
    public function getBorrowedBookByUuid(string $uuid)
    {
        return $this->borrowedBookRepository->findByUuid($uuid);
    }

    /**
     * Retrieve a borrowed book by its UUID and user ID.
     *
     * @param  string  $uuid  The UUID of the borrowed book to retrieve.
     * @param  int  $userId  The ID of the user who borrowed the book.
     * @return \App\Models\BorrowedBook|null The borrowed book if found, null otherwise.
     */
    public function getBorrowedBookByUuidAndUser($uuid, $userId)
    {
        return $this->borrowedBookRepository->findByUuidAndUser($uuid, $userId);
    }

    /**
     * Mark a borrowed book as returned.
     *
     * This method soft deletes the borrowed book, updates the book status to available,
     * and optionally stores the return date in a separate column.
     *
     * @param  \App\Models\BorrowedBook  $borrowedBook  The borrowed book to mark as returned.
     * @param  \Illuminate\Support\Carbon  $returnDate  The date the book was returned.
     * @return void
     */
    public function markBookAsReturned($borrowedBook, $returnDate, $penaltyAmount = 0)
    {
        $borrowedBook->update([
            'return_date' => $returnDate,
            'penalty_paid' => ($penaltyAmount == 0), // If no penalty, mark as paid
            'penalty_amount' => $penaltyAmount,
            'status' => 1, // Mark as returned
        ]);

        $borrowedBook->book->update(['status' => BookStatusEnum::AVAILABLE->value]);
    }

    /**
     * Retrieve all overdue books as of the given date.
     *
     * @param  \Illuminate\Support\Carbon  $currentDate  The date to check for overdue books.
     * @return \Illuminate\Database\Eloquent\Collection The collection of overdue books.
     */
    public function getOverdueBooks($currentDate)
    {
        return $this->borrowedBookRepository->getOverdueBooks($currentDate);
    }

    /**
     * Retrieve the return history of the given user.
     *
     * @param  int  $userId  The user ID to retrieve the return history for.
     * @return \Illuminate\Database\Eloquent\Collection The collection of returned books.
     */
    public function getUserReturnHistory($userId)
    {
        return $this->borrowedBookRepository->getUserReturnHistory($userId);
    }
}
