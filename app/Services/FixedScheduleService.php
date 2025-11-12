<?php

namespace App\Services;

use App\Models\FixedSchedule;

class FixedScheduleService
{
    public function filterFixedSchedules(array $filters)
    {
        $query = FixedSchedule::with(['room']);

        if (!empty($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }

        if (!empty($filters['day_of_week'])) {
            $query->where('day_of_week', 'like', '%' . $filters['day_of_week'] . '%');
        }

        if (!empty($filters['start_time'])) {
            $query->where('start_time', '>=', $filters['start_time']);
        }

        if (!empty($filters['end_time'])) {
            $query->where('end_time', '<=', $filters['end_time']);
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        if (($filters['per_page'] ?? null) === 'all') {
            return $query->get();
        }

        return $query->paginate($filters['per_page'] ?? 99);
    }
}
