<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Rooms;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Rooms::all();
        return RoomResource::collection($rooms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomRequest $request)
    {
        $room = Rooms::create($request->validated());
        return response()->json([
            'Message' => 'Room Created Successfully',
            'data' => new RoomResource($room),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $room = Rooms::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        return new RoomResource($room);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, string $id)
    {
        $room = Rooms::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        $room->update($request->validated());

        return new RoomResource($room);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rooms $room)
    {
        $hasActiveReservation = $room->reservations()
            ->where('status', 'active')
            ->exists();

        if ($hasActiveReservation) {
            return response()->json([
                'message' => 'Ruangan tidak bisa dihapus karena masih ada reservasi aktif.'
            ], 400);
        }

        $room->delete();

        return response()->json([
            'message' => 'Room deleted successfully'
        ]);
    }
}
