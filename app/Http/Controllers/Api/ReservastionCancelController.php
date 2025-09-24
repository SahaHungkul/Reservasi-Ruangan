<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservations;
use Illuminate\Http\Request;

class ReservastionCancelController extends Controller
{
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
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

        return response()->json($reservation);
    }
}
