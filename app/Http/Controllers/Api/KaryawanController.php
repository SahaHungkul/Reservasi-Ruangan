<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FixedSchedule;
use App\Models\Reservations;
use App\Models\Rooms;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\GetReservationRequest;
use App\Helpers\ApiResponse;
use App\Http\Requests\GetRoomRequest;
use App\Http\Resources\PaginatedResource;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\RoomResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use App\Services\ReservationService;

class KaryawanController extends Controller
{

    public function __construct(protected ReservationService $reservationService){}
    /**
     * Display a listing of rooms for karyawan dashboard.
     */
    public function index(GetRoomRequest $request)
    {
        $rooms = Rooms::search($request->search)->latest()->paginate($request->limit ?? 10);

        return ApiResponse::success(
            new PaginatedResource($rooms, RoomResource::class),
            "List Ruangan"
        );
    }

    /**
     * Display a listing of reservations for the authenticated karyawan.
     */
    public function riwayat(GetReservationRequest $request)
    {
        $reservations = Reservations::where('user_id', Auth::id())
            ->search($request->search)
            ->latest()
            ->paginate($request->limit ?? 10);

        return ApiResponse::success(
            new PaginatedResource($reservations, ReservationResource::class),
            "List Reservasi Karyawan"
        );
    }

    /**
     * Store a newly created reservation for the authenticated karyawan.
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // Validasi room
            $room = Rooms::find($data['room_id']);
            if (!$room) {
                DB::rollBack();
                return ApiResponse::error('Ruangan tidak ditemukan', 404);
            }

            // Cek status real-time ruangan (Pendekatan B)
            $roomStatus = $this->reservationService->getRoomRealtimeStatus($room->id);
            if ($roomStatus === 'active') {
                DB::rollBack();
                return ApiResponse::error('Ruangan sedang aktif digunakan', 400);
            }

            // Cek konflik jadwal tetap → auto rejected
            $fixedConflict = $this->reservationService->checkFixedScheduleConflict(
                $data['room_id'], $data['date'], $data['start_time'], $data['end_time']
            );

            if ($fixedConflict) {
                $reason = "Otomatis ditolak: Bentrok dengan jadwal tetap ({$fixedConflict->description}) "
                        . "pada hari {$fixedConflict->day_label} pukul "
                        . date('H:i', strtotime($fixedConflict->start_time)) . "-"
                        . date('H:i', strtotime($fixedConflict->end_time));

                $reservation = $this->reservationService->createReservation($data, 'rejected', $reason);
                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi otomatis ditolak karena bentrok dengan jadwal tetap',
                    'data'    => new ReservationResource($reservation->load(['user', 'room'])),
                ], 400);
            }

            // Cek konflik dengan reservasi lain
            if ($this->reservationService->checkReservationConflict(
                $data['room_id'], $data['date'], $data['start_time'], $data['end_time']
            )) {
                DB::rollBack();
                return ApiResponse::error('Ruangan sudah direservasi pada waktu tersebut', 400);
            }

            // Buat reservasi
            $reservation = $this->reservationService->createReservation($data, 'pending');
            DB::commit();

            return ApiResponse::success(
                new ReservationResource($reservation->load(['user', 'room'])),
                'Reservasi berhasil dibuat dan menunggu persetujuan',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat reservasi karyawan: ' . $e->getMessage());
            return ApiResponse::error('Gagal membuat reservasi', 500);
        }
    }

    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string|max:255',
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

        activity('reservation')
            ->causedBy(Auth::user())
            ->performedOn($reservation)
            ->event('canceled')
            ->log('Reservasi dibatalkan oleh pengguna.');

        // Mail::to('admin@example.com')->send(new ReservationNotificationMail($reservation, 'canceled'));

        return response()->json([
            'success' => true,
            'message' => 'Reservasi berhasil dibatalkan.',
            'data' => new ReservationApprovalResource($reservation),
        ], 200);
    }
}
