<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

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
            'date'       => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            // 'status'     => 'in:pending,approved,rejected,used,canceled',
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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $validator->getData(); // <- PASTI ADA (array)
            if (empty($data['start_time']) || empty($data['end_time'])) {
                return;
            }

            try {
                $start = Carbon::parse($data['start_time']);
                $end   = Carbon::parse($data['end_time']);
                $today = now();
            } catch (\Exception $e) {
                $validator->errors()->add('start_time', 'Format waktu tidak valid.');
                return;
            }

            $diffInMinutes = $start->diffInMinutes($end, false);

            if ($diffInMinutes < 20) {
                $validator->errors()->add('end_time', 'Durasi reservasi minimal 20 menit.');
            }

            if ($diffInMinutes > 180) {
                $validator->errors()->add('end_time', 'Durasi reservasi maksimal 3 jam.');
            }

            if ($start->isBefore($today->startOfDay())) {
                $validator->errors()->add('start_time', 'Tanggal reservasi tidak boleh di masa lalu.');
            }

            if ($start->greaterThan($today->copy()->addDays(30)->endOfDay())) {
                $validator->errors()->add('start_time', 'Reservasi hanya bisa dibuat maksimal 30 hari ke depan.');
            }
        });
    }
}
