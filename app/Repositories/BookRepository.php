<?php

namespace App\Repositories;

use App\Models\Book;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BooksImport;
use App\Exports\BooksExport;

class BookRepository extends BaseRepository
{
    public function __construct()
    {
        $this->model = new Book();
    }

    /**
     * Import books from a CSV file.
     *
     * @param  string  $file    The uploaded file.
     * @param  int     $adminId The admin ID of the user performing the import.
     * @return void
     */
    public function importBooks($file, $adminId)
    {
        Excel::import(new BooksImport($adminId), $file);
    }

    /**
     * Export all books to a CSV file.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportBooks()
    {
        return Excel::download(new BooksExport, 'books.xlsx');
    }

    public function searchBooks($searchTerm)
    {
        return $this->model->whereRaw(
            "to_tsvector('english', title || ' ' || author || ' ' || \"ISBN\" || ' ' || status) @@ plainto_tsquery('english', ?)",
            [$searchTerm]
        )->get();
    }
}
