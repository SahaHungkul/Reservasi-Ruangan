<?php

namespace App\Services;

use App\Models\Rooms;
use Illuminate\Http\Request;

class RoomService
{
    public function filterRooms($request)
    {
        $query = Rooms::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('capacity')) {
            $query->where('capacity', '>=', $request->input('capacity'));
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'asc');

        $allowedSorts = ['created_at', 'name', 'capacity', 'status'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        $perPage = $request->get('per_page', 10);

        if($perPage === 'all'){
            $room = $query->get();
        } else {
            $room = $query->paginate((int) $perPage);
        }

        return $room;
    }
}
