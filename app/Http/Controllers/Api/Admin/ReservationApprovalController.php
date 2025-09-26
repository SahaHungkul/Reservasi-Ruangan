<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationApprovalResource;
use App\Models\Reservations;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationNotificationMail;

class ReservationApprovalController extends Controller
{
    public function approve($id)
    {
        $reservation = Reservations::findOrFail($id);

        $reservation->update([
            'status' => 'approved',
            'reason' => null,
        ]);

        Mail::to($reservation->user->email)->send(new ReservationNotificationMail($reservation, 'approved'));
        $room = Rooms::find($reservation->room_id);
        if ($room) {
            $room->update(['status' => 'active']);
        }

        return new ReservationApprovalResource($reservation);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ], [
            'reason.required' => 'Alasan penolakan wajib diisi.',
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
}
