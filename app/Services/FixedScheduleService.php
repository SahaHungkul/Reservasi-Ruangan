<?php

namespace App\Services;

use App\Models\FixedSchedule;

class FixedScheduleService
{
    public function FilterFixedSchedules($request)
    {
        $query = FixedSchedule::query();

        if (!empty($request->input('room_id'))) {
            $query->where('room_id', $request->input('room_id'));
        }

        if (!empty($request->input('day_of_week'))) {
            $query->where('day_of_week', strtolower($request->input('day_of_week')));
        }

        if (!empty($request->input('start_time'))) {
            $query->where('start_time', '>=', $request->start_time);
        }

        if (!empty($request->input('end_time'))) {
            $query->where('end_time', '<=', $request->end_time);
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSorts = ['created_at', 'day_of_week', 'start_time', 'end_time'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }

        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 10);

        if($perPage === 'all'){
            $fixedSchedules = $query->get();
        } else {
            $fixedSchedules = $query->paginate((int) $perPage);
        }

        return $fixedSchedules;
    }
}
