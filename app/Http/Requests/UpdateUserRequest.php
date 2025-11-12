<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name'=> 'sometimes|string|max:15',
            'email' => [
                'sometimes',
                'email',
                // Rule::unique('user')->ignore(request()->route('id')),
            ],
            'role' => 'sometimes|in:admin,karyawan',
            'password' => 'sometimes|string|min:8',
        ];
    }
}
