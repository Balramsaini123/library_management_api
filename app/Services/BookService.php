<?php

namespace App\Services;


use App\Repositories\BookRepository;
use Illuminate\Support\Facades\Auth;


class BookService
{
    protected $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function create(array $data)
    {
        $data['status'] = $data['status'] ?? 1;
        return $this->bookRepository->create($data);
    }

    public function getAllBooks($searchTerm = null)
    {
        if ($searchTerm) {
            return $this->bookRepository->searchBooks($searchTerm);
        }
        return $this->bookRepository->getAll();
    }

    public function getBookByUuid(string $uuid)
    {
        return $this->bookRepository->findByUuid($uuid);
    }


    public function updateBook($book, array $data)
    {
        return $this->bookRepository->update($book, $data);
    }

    public function deleteBook($book)
    {
        return $this->bookRepository->delete($book);
    }

    public function importBooks($file)
    {
        $user = Auth::user();
        $adminId = $user->id;
        $this->bookRepository->importBooks($file, $adminId);
    }

    public function exportBooks()
    {
        return $this->bookRepository->exportBooks();
    }
}
