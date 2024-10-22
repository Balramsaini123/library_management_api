<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:50',
            'description' => 'required',
            'author' => 'required|string|max:50',
            'ISBN' => 'required|string|max:13|unique:books',
            'price' => 'required',
            'published_date' => 'required|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title is required',
            'title.max' => 'Title cannot exceed 50 characters',
            'description.required' => 'Description is required',
            'author.required' => 'Author is required',
            'author.max' => 'Author cannot exceed 50 characters',
            'ISBN.required' => 'ISBN is required',
            'ISBN.unique' => 'ISBN already exists',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'published_date.required' => 'Published date is required',
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
