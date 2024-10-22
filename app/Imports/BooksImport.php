<?php

namespace App\Imports;

use App\Models\Book;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class BooksImport implements ToModel, WithHeadingRow
{

    protected $adminId;

    public function __construct($adminId)
    {
        $this->adminId = $adminId; // Store the admin ID
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $row['isbn'] = (string) $row['isbn'];
        // Define the validation rules for each row
        $validator = Validator::make($row, [
            'title' => 'required|string|max:50',
            'author' => 'required|string|max:50',
            'isbn' => 'required|string|max:13|unique:books,ISBN',
            'published_date' => 'required|date_format:Y-m-d',  // Ensure the date is in correct format
            'status' => 'required|in:0,1',
            'price' => 'required|numeric',
            'description' => 'required|string',
        ]);

        // If validation fails, return null to skip the row or handle the error
        if ($validator->fails()) {
            Log::error('Row validation failed', ['errors' => $validator->errors(), 'row' => $row]);
            return null;  // Skip this row if validation fails
        }

        // Return a new Book instance with the validated data
        return new Book([
            'title' => $row['title'],               // Use headings if WithHeadingRow is enabled
            'author' => $row['author'],
            'ISBN' => $row['isbn'],
            'published_date' => $row['published_date'],
            'status' => $row['status'],
            'price' => $row['price'],
            'description' => $row['description'],
            'admin_id' => $this->adminId,
        ]);
    }
}
