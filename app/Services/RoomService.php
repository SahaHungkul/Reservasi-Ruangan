<?php

namespace App\Services;

use App\Models\Rooms;
use Illuminate\Http\Request;

class RoomService
{
    public function filterRooms($request)
    {
        $query = Rooms::query();

        // 🔍 filter berdasarkan nama ruangan

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('capacity')) {
            $query->where('capacity', '>=', $request->input('capacity'));
        }

        $sortBy = $request->get('sort_by', 'created_at'); // field yang ingin diurutkan
        $sortOrder = $request->get('sort_order', 'desc');

        $allowedSorts = ['created_at', 'name', 'capacity', 'status'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($request->get('per_page', 50));
    }
}
