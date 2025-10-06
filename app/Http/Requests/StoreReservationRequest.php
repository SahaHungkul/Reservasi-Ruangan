<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StoreReservationRequest extends FormRequest
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
            'room_id'    => 'required|exists:rooms,id',
            'date'       => 'required|date_format:Y-m-d|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required' => 'Ruangan wajib dipilih',
            'room_id.exists' => 'Ruangan tidak ditemukan',
            'date.required' => 'Tanggal wajib diisi',
            'date.date' => 'Format tanggal tidak valid',
            'date.after_or_equal' => 'Tanggal tidak boleh kurang dari hari ini',
            'start_time.required' => 'Waktu mulai wajib diisi',
            'start_time.date_format' => 'Format waktu mulai tidak valid (HH:mm)',
            'end_time.required' => 'Waktu selesai wajib diisi',
            'end_time.date_format' => 'Format waktu selesai tidak valid (HH:mm)',
            'end_time.after' => 'Waktu selesai harus setelah waktu mulai',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
