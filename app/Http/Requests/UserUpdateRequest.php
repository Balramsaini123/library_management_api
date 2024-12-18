<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
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
            'name' => 'string|max:50',
            'email' => [
                'string',
                'email',
                'max:50',
                Rule::unique('users')->ignore($uuid, 'uuid_column'),
            ],
            'password' => ['string', 'min:6'],
            'phone_no' => ['string', 'max:20'],
            'role' => ['nullable', 'in:1,2,3'],
            'dob' => ['nullable', 'date'],
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
