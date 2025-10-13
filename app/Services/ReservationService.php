<?php

namespace App\Services;

use App\Models\Reservations;
use Illuminate\Http\Request;

class ReservationService
{
    public function filterReservations(Request $request)
    {
        $query = Reservations::with(['user', 'room']);
        $user = $request->user();

         if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('date') && $request->date !== '') {
            $query->where('date', $request->date);
        }
        if ($request->has('day_of_week') && $request->day_of_week !== '') {
            $query->where('day_of_week', $request->day_of_week);
        }
        if ($request->has('user_id') && $request->user_id !== '') {
            $query->where('user_id', $request->user_id);
        }
        if ($request->has('room_id') && $request->room_id !== '') {
            $query->where('room_id', $request->room_id);
        }
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSorts = ['created_at', 'date', 'start_time', 'end_time', 'status'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 10);

        if($perPage === 'all'){
            $reservations = $query->get();
        } else {
            $reservations = $query->paginate((int) $perPage);
        }

        return $reservations;
    }
}
