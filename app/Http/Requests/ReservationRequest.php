<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReservationRequest extends FormRequest
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
            // 'user_id'    => 'required|exists:users,id',
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'status'     => 'in:pending,approved,rejected,used,canceled',
        ];
    }

    public function messages(): array
    {
        return [
            'room_id.required' => 'Ruangan wajib dipilih.',
            'room_id.exists'   => 'Ruangan tidak ditemukan.',
            'user_id.required' => 'User wajib dipilih.',
            'date.required'    => 'Tanggal reservasi wajib diisi.',
            'end_time.after'   => 'Waktu selesai harus setelah waktu mulai.',
        ];
    }
}
