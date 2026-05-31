<?php

namespace App\Services;

use App\Models\FixedSchedule;

class FixedScheduleService
{
    public function checkConflict(int $roomId, string $dayOfWeek, string $startTime, string $endTime, ?int $excludeId = null): bool
    {
        return FixedSchedule::where('room_id', $roomId)
            ->where('day_of_week', $dayOfWeek)
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();
    }

    public function store(array $data): FixedSchedule
    {
        return FixedSchedule::create($data);
    }
}
