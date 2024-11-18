<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Services\BookService;
use App\Services\LoggingService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    use JsonResponseTrait;

    protected $bookService;

    protected $loggingService;

    public function __construct(BookService $bookService, LoggingService $loggingService)
    {
        $this->bookService = $bookService;
        $this->loggingService = $loggingService;
    }

    /**
     * Store a newly created book in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(StoreBookRequest $request)
    {

        $this->authorize('create', Book::class);
        try {
            $user = Auth::user();
            $adminId = $user->id;
            $data = array_merge($request->validated(), ['admin_id' => $adminId]);
            $book = $this->bookService->create($data);
            //book log creation
            $this->loggingService->log('book', 'Book created successfully.', ['book_id' => $book->id, 'admin_id' => $adminId]);

            return $this->successResponse($book, 'messages.book.create', 201);
        } catch (\Exception $e) {
            $this->loggingService->error('book', 'Failed to create book.', ['error' => $e->getMessage()]);

            return $this->errorResponse('messages.book.not_create', 500);
        }
    }

    public function readAll(Request $request)
    {
        try {
            $searchTerm = $request->input('query');
            $books = $this->bookService->getAllBooks($searchTerm);
            $this->loggingService->log('book', 'Books retrieved successfully.');

            return $this->successResponse($books, 'messages.book.books', 200);
        } catch (\Exception $e) {
            $this->loggingService->error('book', 'Fail to find books', ['error' => $e->getMessage()]);

            return $this->errorResponse('messages.book.faild', 500);
        }
    }

    /**
     * Retrieve a book by its UUID from storage.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function read($uuid)
    {
        try {
            $book = $this->bookService->getBookByUuid($uuid);
            if (! $book) {
                return $this->errorResponse('messages.book.notfound', 404);
            }
            $this->loggingService->log('book', 'Book retrieved successfully.', ['book_id' => $book->id]);

            return $this->successResponse($book, 'messages.book.book', 200);
        } catch (\Throwable $th) {
            $this->loggingService->error('book', 'Failed to retrived book.', ['error' => $th->getMessage()]);

            return $this->errorResponse('messages.error.default', 500);
        }
    }

    /**
     * Update a book by its UUID.
     *
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookRequest $request, $uuid)
    {
        try {
            // Find the book by UUID
            $book = $this->bookService->getBookByUuid($uuid);
            if (! $book) {
                return $this->errorResponse('messages.book.notfound', 404);
            }

            // Authorize the action using the specific book instance
            $this->authorize('update', $book);

            // Update the book
            $updatedBook = $this->bookService->updateBook($book, $request->validated());
            $this->loggingService->log('book', 'Book updated successfully.', ['book_id' => $updatedBook->id]);

            return $this->successResponse($updatedBook, 'messages.book.update', 200);
        } catch (\Throwable $th) {
            $this->loggingService->error('book', 'Failed to update book.', ['error' => $th->getMessage()]);

            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Delete a book by its UUID.
     *
     * @param  string  $uuid  The UUID of the book to delete.
     * @return \Illuminate\Http\Response
     */
    public function delete($uuid)
    {
        try {
            $book = $this->bookService->getBookByUuid($uuid);
            if (! $book) {
                return $this->errorResponse('messages.book.notfound', 404);
            }
            $this->authorize('delete', $book);

            $this->bookService->deleteBook($book);
            $this->loggingService->log('book', 'Book deleted successfully.', ['book_id' => $book->id]);

            return $this->successResponse(null, 'messages.book.delete', 200);
        } catch (\Throwable $th) {
            $this->loggingService->error('book', 'Failed to delete book.', ['error' => $th->getMessage()]);

            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Import books from a CSV file.
     *
     * @return \Illuminate\Http\Response
     */
    public function importBooks(Request $request)
    {
        $this->authorize('create', Book::class);
        try {
            $this->bookService->importBooks($request->file('file'));
            $this->loggingService->log('book', 'Books imported successfully.');

            return $this->successResponse(null, 'messages.book.import', 200);
        } catch (\Exception $e) {
            $this->loggingService->error('book', 'Failed to import books.', ['error' => $e->getMessage()]);

            return $this->errorResponse('messages.error.default', 500);
        }
    }

    /**
     * Export all books to a CSV file.
     *
     * @return \Illuminate\Http\Response
     */
    public function exportBooks()
    {
        try {
            $this->bookService->exportBooks();
            $this->loggingService->log('book', 'Books exported successfully.');

            return $this->successResponse(null, 'messages.book.export', 200);
        } catch (\Exception $e) {
            $this->loggingService->error('book', 'Failed to export books.', ['error' => $e->getMessage()]);

            return $this->errorResponse('messages.error.default', 500);
        }
    }
}
