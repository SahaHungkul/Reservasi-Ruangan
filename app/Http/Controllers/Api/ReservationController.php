<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Models\FixedSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Reservations;

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

        $day   = \Carbon\Carbon::parse($data['start_time'])->format('l');
        $start = \Carbon\Carbon::parse($data['start_time'])->format('H:i');
        $end   = \Carbon\Carbon::parse($data['end_time'])->format('H:i');

        $isBlocked = FixedSchedule::where('room_id', $data['room_id'])
            ->where('day', $day)
            ->where('status', 'active')
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
        $validated = $request->validated();
        $reservation = Reservations::create([
            'room_id' => $validated['room_id'],
            'user_id' => Auth::user()->id,
            'date'       => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time'   => $validated['end_time'],
            'status'     => 'pending',
        ]);

        return new ReservationResource($reservation);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $reservation = Reservations::with('room')->findOrFail($id);

        return new ReservationResource($reservation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReservationRequest $request, $id)
    {
        $reservation = Reservations::findOrFail($id);
        $reservation->update($request->validated());

        return new ReservationResource($reservation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $reservation = Reservations::findOrFail($id);
        $reservation->delete();

        return response()->json([
            'message' => 'Reservation deleted successfully'
        ], 200);
    }
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,canceled,used',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in'       => 'Status tidak valid.',
        ]);

        $reservation = Reservations::findOrFail($id);

        if (!$request->user() || $request->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }


        $reservation->update([
            'status' => $request->status,
        ]);

        if ($request->status === 'approved') {
            $reservation->room->update(['status' => 'used']);
        }

        if (in_array($request->status, ['canceled', 'rejected'])) {
            $reservation->room->update(['status' => 'available']);
        }

        return new ReservationResource($reservation);
    }
}
