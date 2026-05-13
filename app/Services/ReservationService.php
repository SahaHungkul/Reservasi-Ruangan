<?php

namespace App\Services;

use App\Models\Reservations;
use App\Models\FixedSchedule;
use App\Models\Rooms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationService
{
    public function getRoomRealtimeStatus(int $roomId): string
    {
        $now = now();

        $isActive = Reservations::where('room_id', $roomId)
            ->where('status', 'approved')
            ->whereDate('date', $now->toDateString())
            ->where('start_time', '<=', $now->format('H:i:s'))
            ->where('end_time', '>', $now->format('H:i:s'))
            ->exists();

        return $isActive ? 'active' : 'inactive';
    }

    public function checkFixedScheduleConflict(int $roomId, string $date, string $startTime, string $endTime): ?FixedSchedule
    {
        $dayOfWeek = strtolower(Carbon::parse($date)->format('l'));

        return FixedSchedule::where('room_id', $roomId)
            ->where('day_of_week', $dayOfWeek)
            ->whereRaw('? < end_time AND ? > start_time', [$startTime, $endTime])
            ->first();
    }

    public function checkReservationConflict(int $roomId, string $date, string $startTime, string $endTime): bool
    {
        return Reservations::where('room_id', $roomId)
            ->where('date', $date)
            ->whereIn('status', ['approved']) // 'completed' tidak ikut terhitung
            ->whereRaw('? < end_time AND ? > start_time', [$startTime, $endTime])
            ->exists();
    }

    public function createReservation(array $data, string $status, ?string $reason = null): Reservations
    {
        $reservation = Reservations::create([
            'user_id'    => Auth::id(),
            'room_id'    => $data['room_id'],
            'date'       => $data['date'],
            'start_time' => $data['start_time'],
            'end_time'   => $data['end_time'],
            'status'     => $status,
            'reason'     => $reason,
        ]);

        $logMessage = match ($status) {
            'rejected' => 'Reservasi baru dibuat dan otomatis ditolak karena bentrok jadwal tetap.',
            'pending'  => 'Reservasi baru dibuat dan menunggu persetujuan.',
            default    => 'Reservasi dibuat.',
        };

        activity('reservation')
            ->causedBy(Auth::user())
            ->performedOn($reservation)
            ->withProperties([
                'date'  => $reservation->date,
                'start' => $reservation->start_time,
                'end'   => $reservation->end_time,
            ])
            ->log($logMessage);

        return $reservation;
    }
}

