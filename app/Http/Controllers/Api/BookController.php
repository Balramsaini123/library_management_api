<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Services\BookService;
use App\Traits\JsonResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    use JsonResponseTrait;
    protected $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }
    /**
     * Store a newly created book in storage.
     *
     * @param  \App\Http\Requests\StoreBookRequest  $request
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
            return $this->successResponse($book, 'messages.book.create', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('messages.book.not_create', 500);
        }
    }

    
    public function readAll(Request $request)
    {
        try {
            $searchTerm = $request->input('query');
            $books = $this->bookService->getAllBooks($searchTerm);

            return $this->successResponse($books, 'messages.book.books', 200);
        } catch (\Exception $e) {
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
            if (!$book) {
                return $this->errorResponse('messages.book.notfound', 404);
            }

            return $this->successResponse($book, 'messages.book.book', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse('messages.error.default', 500);
        }
    }

    /**
     * Update a book by its UUID.
     *
     * @param  \App\Http\Requests\UpdateBookRequest  $request
     * @param  string  $uuid
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookRequest $request, $uuid)
    {
        try {
            // Find the book by UUID
            $book = $this->bookService->getBookByUuid($uuid);
            if (!$book) {
                return $this->errorResponse('messages.book.notfound', 404);
            }

            // Authorize the action using the specific book instance
            $this->authorize('update', $book);

            // Update the book
            $updatedBook = $this->bookService->updateBook($book, $request->validated());
            return $this->successResponse($updatedBook, 'messages.book.update', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Delete a book by its UUID.
     *
     * @param string $uuid The UUID of the book to delete.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($uuid)
    {
        try {
            $book = $this->bookService->getBookByUuid($uuid);
            if (!$book) {
                return $this->errorResponse('messages.book.notfound', 404);
            }
            $this->authorize('delete', $book);

            $this->bookService->deleteBook($book);
            return $this->successResponse(null, 'messages.book.delete', 200);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Import books from a CSV file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importBooks(Request $request)
    {
        $this->authorize('create', Book::class);
        try {
            $this->bookService->importBooks($request->file('file'));

            return $this->successResponse(null, 'messages.book.import', 200);
        } catch (\Exception $e) {
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
            return $this->successResponse(null, 'messages.book.export', 200);
        } catch (\Exception $e) {
            return $this->errorResponse('messages.error.default', 500);
        }
    }
}
