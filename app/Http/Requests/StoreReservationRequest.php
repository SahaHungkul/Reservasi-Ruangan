<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Carbon\Carbon;

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

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $data = $this->validated();

            if (!empty($data['start_time']) && !empty($data['end_time'])) {
                $start = Carbon::parse($data['date'] . ' ' . $data['start_time']);
                $end   = Carbon::parse($data['date'] . ' ' . $data['end_time']);
                $today = today();
                $diffInMinutes = $start->diffInMinutes($end);

                if ($diffInMinutes > 180) {
                    $validator->errors()->add('end_time', 'Durasi Maksimal 3 jam');
                }
                $limit = now()->addDays(30)->endOfDay();
                if ($start->gt($limit)){
                    $validator->errors()->add('date', 'Reservasi maksimal 30 hari kedepan');
                }
            }
        });
    }
}
