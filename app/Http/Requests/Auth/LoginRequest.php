<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ];
    }

    public function message()
    {
        return [
            'email.required' => 'Email harap diisi',
            'email.email' => 'Email tidak valid',
            'password.required' => 'Password harap diisi',
            'password.password' => 'Password tidak sesuai',
            'password.min' => 'Password minimal 8 karakter',
        ];
    }
}
