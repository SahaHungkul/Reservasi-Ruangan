<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class UpdateFixedScheduleRequest extends FormRequest
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
            'room_id' => 'sometimes|exists:rooms,id',
            'day_of_week' => ['sometimes', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'description' => 'sometimes|nullable|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.exists'   => 'Ruangan tidak ditemukan.',
            'day_of_week.in' => 'Hari tidak valid.',
            'start_time.date_format' => 'Format jam mulai tidak valid. Gunakan format HH:MM.',
            'end_time.date_format'   => 'Format jam selesai tidak valid. Gunakan format HH:MM.',
            'end_time.after'      => 'Jam selesai harus lebih besar dari jam mulai.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
