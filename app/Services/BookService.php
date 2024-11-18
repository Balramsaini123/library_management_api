<?php

namespace App\Services;

use App\Enums\BookStatusEnum;
use App\Repositories\BookRepository;
use Illuminate\Support\Facades\Auth;

class BookService
{
    protected $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    /**
     * Create a new book.
     *
     * @param  array  $data  The book data to be created.
     * @return \App\Models\Book The created book.
     */
    public function create(array $data)
    {
        $data['status'] = $data['status'] ?? BookStatusEnum::AVAILABLE->value;

        return $this->bookRepository->create($data);
    }

    /**
     * Retrieve all books, or search for books by the given search term.
     *
     * @param  string|null  $searchTerm  The search term to search for books.
     * @return \Illuminate\Database\Eloquent\Collection The collection of books.
     */
    public function getAllBooks($searchTerm = null)
    {
        if ($searchTerm) {
            return $this->bookRepository->searchBooks($searchTerm);
        }

        return $this->bookRepository->getAll();
    }

    /**
     * Retrieve a book by its UUID.
     *
     * @param  string  $uuid  The UUID of the book to retrieve.
     * @return \App\Models\Book|null The book if found, null otherwise.
     */
    public function getBookByUuid(string $uuid)
    {
        return $this->bookRepository->findByUuid($uuid);
    }

    /**
     * Update a book.
     *
     * @param  mixed  $book  The book to be updated (instance of Book or its UUID).
     * @param  array  $data  The book data to be updated.
     * @return \App\Models\Book The updated book.
     */
    public function updateBook($book, array $data)
    {
        return $this->bookRepository->update($book, $data);
    }

    /**
     * Delete a book.
     *
     * @param  mixed  $book  The book to be deleted (instance of Book or its UUID).
     * @return bool True if the book is deleted, false otherwise.
     */
    public function deleteBook($book)
    {
        return $this->bookRepository->delete($book);
    }

    /**
     * Import books from a CSV file.
     *
     * @param  \Illuminate\Http\UploadedFile  $file  The uploaded CSV file.
     */
    public function importBooks($file)
    {
        $user = Auth::user();
        $adminId = $user->id;
        $this->bookRepository->importBooks($file, $adminId);
    }

    /**
     * Export all books to a CSV file.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportBooks()
    {
        return $this->bookRepository->exportBooks();
    }
}
