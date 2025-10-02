<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\ReservationApprovalResource;
use App\Http\Resources\ReservationResource;
use App\Models\FixedSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Reservations;
use App\Mail\ReservationNotificationMail;
use App\Models\Rooms;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservations::with('room')->latest()->get();

        return ReservationResource::collection($reservations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReservationRequest $request)
    {
        $data = $request->validated();

        $date = Carbon::parse($data['date']);
        $day  = $date->locale('id')->dayName;

        $start = Carbon::parse($data['start_time'])->format('H:i');
        $end   = Carbon::parse($data['end_time'])->format('H:i');

        DB::beginTransaction();

        try {

            // ✅ Cek Fixed Schedule
            $isBlocked = FixedSchedule::where('room_id', $data['room_id'])
                ->where('day_of_week', $day)
                ->where(function ($query) use ($start, $end) {
                    $query->whereBetween('start_time', [$start, $end])
                        ->orWhereBetween('end_time', [$start, $end])
                        ->orWhere(function ($q) use ($start, $end) {
                            $q->where('start_time', '<=', $start)
                                ->where('end_time', '>=', $end);
                        });
                })
                ->exists();

            if ($isBlocked) {
                return response()->json([
                    'message' => 'Reservasi ditolak karena bentrok dengan jadwal tetap.'
                ], 422);
            }

            // ✅ Cek bentrok dengan reservasi approved
            $hasConflict = Reservations::where('room_id', $data['room_id'])
                ->where('status', 'approved')
                ->where(function ($query) use ($data) {
                    $query->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                        ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']])
                        ->orWhere(function ($q) use ($data) {
                            $q->where('start_time', '<=', $data['start_time'])
                                ->where('end_time', '>=', $data['end_time']);
                        });
                })
                ->exists();

            if ($hasConflict) {
                return response()->json([
                    'message' => 'Reservasi ditolak karena sudah ada reservasi lain.'
                ], 422);
            }

            // ✅ Simpan reservasi baru
            $reservation = Reservations::create([
                'room_id'    => $data['room_id'],
                'user_id'    => Auth::id(),
                'date'       => $data['date'],
                'day' => Carbon::parse($data['start_time'])->format('l'),
                'start_time' => $data['start_time'],
                'end_time'   => $data['end_time'],
                // 'status'     => 'pending',
            ]);

            // Mail::to('admin@example.com')->send(new ReservationNotificationMail($reservation, 'pending'));
            DB::commit();
            return new ReservationResource($reservation);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat reservasi: ' . $e->getMessage());

            return response()->json([
                'message' => 'Terjadi kesalahan, reservasi gagal dibuat.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $reservation = Reservations::with('room')->findOrFail($id);

        return new ReservationResource($reservation);
    }

    public function approve(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'nullable|string|max:255',
            ]);

            $reservation = Reservations::findOrFail($id);

            if (in_array($reservation->status, ['canceled', 'rejected'])) {
                return response()->json([
                    'message' => 'Reservasi yang sudah dibatalkan atau ditolak tidak bisa di-approve lagi.'
                ], 422);
            }

            $reservation->update([
                'status' => 'approved',
                'reason' => $request->input('reason'),
            ]);

            // Mail::to($reservation->user->email)->send(new ReservationNotificationMail($reservation, 'approved'));

            $room = Rooms::find($reservation->room_id);
            if ($room) {
                $room->update(['status' => 'active']);
            }

            Reservations::where('room_id', $reservation->room_id)
                ->where('id', '!=', $reservation->id)
                ->where('status', 'pending')
                ->where(function ($query) use ($reservation) {
                    $query->whereBetween('start_time', [$reservation->start_time, $reservation->end_time])
                        ->orWhereBetween('end_time', [$reservation->start_time, $reservation->end_time])
                        ->orWhere(function ($q) use ($reservation) {
                            $q->where('start_time', '<=', $reservation->start_time)
                                ->where('end_time', '>=', $reservation->end_time);
                        });
                })
                ->update([
                    'status' => 'rejected',
                    'reason' => 'Ditolak otomatis karena jadwal sudah diambil reservasi lain.',
                ]);

            return new ReservationApprovalResource($reservation);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'gagal approve reservasi.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',

            // [
            //     'reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $reservation = Reservations::findOrFail($id);

        $reservation->update([
            'status' => 'rejected',
            'reason' => $request->reason,
        ]);

        // Mail::to($reservation->user->email)->send(new ReservationNotificationMail($reservation, 'rejected'));

        $room = Rooms::find($reservation->room_id);
        if ($room) {
            $room->update(['status' => 'inactive',]);
        }

        return new ReservationApprovalResource($reservation);
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $reservation = Reservations::findOrFail($id);

        if (!in_array($reservation->status, ['pending', 'approved'])) {
            return response()->json([
                'message' => 'Reservasi tidak bisa dibatalkan.',
            ], 422);
        }

        $reservation->update([
            'status' => 'canceled',
            'reason' => $request->reason,
        ]);
        // Mail::to('admin@example.com')->send(new ReservationNotificationMail($reservation, 'canceled'));

        return new ReservationApprovalResource($reservation);
    }
}
