<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BookService;
use App\Services\BorrowedBookService;
use App\Traits\JsonResponseTrait;
use Illuminate\Support\Facades\Auth;

class BorrowedBookController extends Controller
{
    use JsonResponseTrait;

    protected $bookService;

    protected $borrowedBookService;

    public function __construct(BookService $bookService, BorrowedBookService $borrowedBookService)
    {
        $this->borrowedBookService = $borrowedBookService;
        $this->bookService = $bookService;
    }

    /**
     * Borrow a book by its UUID.
     *
     * @param  string  $uuid  The UUID of the book to borrow.
     * @return \Illuminate\Http\Response
     */
    public function borrowBook(string $uuid)
    {
        try {
            $user = Auth::user();
            $book = $this->bookService->getBookByUuid($uuid);
            if (! $book || $book->status == 0) {
                return $this->errorResponse('messages.book.notfound', 404);
            }
            $borrowedBook = $this->borrowedBookService->borrowBook($book, $user);

            return $this->successResponse($borrowedBook, 'messages.book.borrowed', 201);
        } catch (\Throwable $th) {
            return $this->errorResponse('messages.error.default', 500);
        }
    }

    /**
     * Retrieve all books borrowed by the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserBorrowedBooks()
    {
        try {
            $borrowedBooks = $this->borrowedBookService->getUserBorrowedBooks();

            return $this->successResponse($borrowedBooks, 'messages.book.borrowed_books', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse('messages.error.default', 500);
        }
    }

    /**
     * Retrieve users who have borrowed a specific book by its UUID.
     *
     * @param  string  $uuid  The UUID of the book to check.
     * @return \Illuminate\Http\Response
     */
    public function getUsersByBook(string $uuid)
    {
        try {
            $book = $this->bookService->getBookByUuid($uuid);
            if (! $book) {
                return $this->errorResponse('messages.book.notfound', 404);
            }
            $this->authorize('viewAny', $book);
            $borrowedBooks = $this->borrowedBookService->getUsersByBook($book);

            return $this->successResponse($borrowedBooks, 'messages.book.borrowed_books', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('messages.book.failed_to_retrieve', 500);
        }
    }

    /**
     * Return a borrowed book by its UUID.
     *
     * @param  string  $uuid  The UUID of the book to return.
     * @return \Illuminate\Http\Response
     */
    public function returnBook(string $uuid)
    {
        $responseMessage = '';
        $responseStatus = 200;

        try {
            $user = Auth::user();
            $borrowedBook = $this->borrowedBookService->getBorrowedBookByUuidAndUser($uuid, $user->id);
            if (! $borrowedBook) {
                $responseMessage = 'messages.book.not_borrowed';
                $responseStatus = 404;
            } else {
                $currentDate = now();
                $penaltyAmount = 0;
                if ($currentDate->gt($borrowedBook->due_date)) {
                    $penaltyAmount = 10;
                    $responseMessage = 'Due date exceeded. Contact the library and pay the late fee.';
                    $responseStatus = 400;
                } else {
                    $this->borrowedBookService->markBookAsReturned($borrowedBook, $currentDate, $penaltyAmount);
                    $responseMessage = 'Book returned successfully.';
                }
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            $responseMessage = 'An error occurred while returning the book.';
            $responseStatus = 500;
        }

        return $this->successResponse(null, $responseMessage, $responseStatus);
    }

    /**
     * Retrieve all overdue books.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOverdueBooks()
    {
        try {
            $currentDate = now();
            $overdueBooks = $this->borrowedBookService->getOverdueBooks($currentDate);
            if (! $overdueBooks) {
                return $this->successResponse($overdueBooks, 'messages.book.overdue_books', 200);
            }

            return $this->errorResponse('messages.book.not_overdue_books', 404);
        } catch (\Throwable $th) {
            return $this->errorResponse('messages.error.default', 500);
        }
    }

    /**
     * Retrieve the return history of the current user.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUserReturnHistory()
    {
        try {
            $user = Auth::user();
            $returnHistory = $this->borrowedBookService->getUserReturnHistory($user->id);

            return $this->successResponse($returnHistory, 'messages.book.return_history', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse('messages.error.default', 500);
        }
    }
}
