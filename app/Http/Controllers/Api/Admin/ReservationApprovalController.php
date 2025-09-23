<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationApprovalResource;
use App\Models\Reservations;
use App\Models\Rooms;

class ReservationApprovalController extends Controller
{
    public function approve($id)
    {
        $reservation = Reservations::findOrFail($id);

        $reservation->update(['status' => 'approved']);
        $room = Rooms::find($reservation->room_id);
        if($room){
            $room->update(['status' => 'active']);
        }

        return new ReservationApprovalResource($reservation);
    }

    public function reject($id)
    {
        $reservation = Reservations::findOrFail($id);

        $reservation->update(['status' => 'rejected']);

        $room = Rooms::find($reservation->room_id);
        if($room){
            $room->update(['status'=> 'inactive']);
        }

        return new ReservationApprovalResource($reservation);
    }
}
