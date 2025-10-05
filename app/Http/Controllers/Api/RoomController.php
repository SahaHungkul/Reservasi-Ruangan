<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoomRequest;
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
        try {
            $rooms = Rooms::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => RoomResource::collection($rooms)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data ruangan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoomRequest $request)
    {
        try {
            $validated = $request->validated();
            $room = Rooms::create([
                'name' => $validated['name'],
                'capacity' => $validated['capacity'],
                'description' => $validated['description'] ?? null,
                'status' => 'inactive',
            ]);

            return response()->json([
                'Success' => true,
                'Message' => 'Room Created Successfully',
                'Data' => new RoomResource($room),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'Success' => false,
                'Message' => 'Room Creation Failed: ',
                'Error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $room = Rooms::find($id);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found'
                ], 404);
            }

            return response()->json([
                'Success' => true,
                'Data' => new RoomResource($room),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'Success' => false,
                'Message' => 'Failed to retrieve room: ',
                'Error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, string $id)
    {
        try {
            $room = Rooms::find($id);

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found'
                ], 404);
            }

            if ($room->status === 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruangan tidak dapat diubah karena masih dalam status aktif'
                ], 400);
            }

            if ($room->reservations()->where('status', 'active')->exists()) {
                return response()->json([
                    'message' => 'Ruangan tidak bisa edit karena masih ada reservasi aktif.'
                ], 400);
            }

            $room->update($request->validated());

            return response()->json([
                'Success' => true,
                'Message' => 'Room updated successfully',
                'Data' => new RoomResource($room),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'Success' => false,
                'Message' => 'Room Update Failed: ',
                'Error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rooms $room, $id)
    {
        try {
            $room = Rooms::find($id);

            if ($room->status === 'active') {
                return response()->json([
                    'message' => 'Ruangan masih aktif. Nonaktifkan dulu sebelum dihapus.'
                ], 400);
            }

            if ($room->reservations()->where('status', 'active')->exists()) {
                return response()->json([
                    'message' => 'Ruangan tidak bisa dihapus karena masih ada reservasi aktif.'
                ], 400);
            }

            $room->delete();

            return response()->json([
                'Success' => true,
                'Message' => 'Room deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus ruangan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getActiveRooms()
    {
        try {
            $rooms = Rooms::where('status', 'active')
                ->orderBy('name', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => RoomResource::collection($rooms)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data ruangan aktif',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
