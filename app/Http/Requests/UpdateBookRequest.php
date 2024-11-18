<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $uuid = $this->route('uuid');

        return [
            'title' => 'string|max:50',
            'description' => 'sometimes',
            'author' => 'string|max:50',
            'ISBN' => 'string|max:13', Rule::unique('books')->ignore($uuid, 'uuid_column'), // Ensure ISBN is unique but ignore current book
            'price' => 'sometimes|numeric',
            'published_date' => 'sometimes|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'Title must be a valid string',
            'title.max' => 'Title cannot exceed 50 characters',
            'description.string' => 'Description must be valid',
            'author.string' => 'Author must be a valid string',
            'author.max' => 'Author cannot exceed 50 characters',
            'ISBN.unique' => 'ISBN already exists',
            'price.numeric' => 'Price must be a number',
            'published_date.date' => 'Published date must be a valid date',
        ];
    }

    public function withValidator($validator)
    {
        $input = $this->all();
        $allowedFields = array_keys($this->rules());
        $extraFields = array_diff(array_keys($input), $allowedFields);

        // If there are extra fields, fail the validation with a custom message
        if (count($extraFields) > 0) {
            $validator->after(function ($validator) use ($extraFields) {
                foreach ($extraFields as $extraField) {
                    $validator->errors()->add($extraField, 'This field does not exist.');
                }
            });
        }
    }
}
