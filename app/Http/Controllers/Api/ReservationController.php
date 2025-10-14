<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FixedSchedule;
use App\Models\Reservations;
use App\Models\Rooms;
use App\Http\Requests\StoreReservationRequest;
use App\Http\Resources\ReservationApprovalResource;
use App\Http\Resources\ReservationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\ReservationNotificationMail;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $validated = $request->validate([
                'date' => 'nullable|date',
                'day_of_week' => 'nullable|string',
                'user_id' => 'nullable|int|min:1',
                'room_id' => 'nullable|int|min:1',
                'status' => 'nullable|string|in:active,inactive',
                'user_name'=> 'nullable|string',
                'room_name' => 'nullable|string',
                'sort_order' => 'nullable|string|in:asc,desc',
                'per_page' => [
                    'nullable',
                    function ($attribute, $value, $fail) {
                        if ($value === 'all') return; // valid

                        if (!ctype_digit(strval($value)) || (int)$value < 1) {
                            $fail("The $attribute field must be a positive integer or 'all'.");
                        }
                    },
                ],
                'page' => 'nullable|int|min:1',
            ]);
            $reservations = $this->reservationService->filterReservations($validated);

            return response()->json([
                'success' => true,
                'pagination' => [
                    'per_page' => $reservations->perPage(),
                    'page' => $reservations->currentPage() . '/' . $reservations->lastPage(),
                    'total' => $reservations->total(),
                ],
                'data' => $reservations->isEmpty() ? [null] : ReservationResource::collection($reservations)
            ], 200);
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data reservasi: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengambil data reservasi.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Get day of week from date
            $date = Carbon::parse($validated['date']);
            $dayOfWeek = strtolower($date->format('l'));

            // Cek room ada
            $room = Rooms::find($validated['room_id']);
            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruangan tidak ditemukan'
                ], 404);
            }

            // CEK 1: Fixed Schedule Conflict (Auto Reject)
            $fixedConflict = FixedSchedule::where('room_id', $validated['room_id'])
                ->where('day_of_week', $dayOfWeek)
                ->whereRaw('? < end_time AND ? > start_time', [
                    $validated['start_time'],
                    $validated['end_time']
                ])
                ->first();

            if ($fixedConflict) {
                // Auto rejected karena bentrok dengan jadwal tetap
                $reservation = Reservations::create([
                    'user_id' => Auth::id(),
                    'room_id' => $validated['room_id'],
                    'date' => $validated['date'],
                    'start_time' => $validated['start_time'],
                    'end_time' => $validated['end_time'],
                    'status' => 'rejected',
                    'reason' => "Otomatis ditolak: Bentrok dengan jadwal tetap ({$fixedConflict->description}) pada hari {$fixedConflict->day_label} pukul " . date('H:i', strtotime($fixedConflict->start_time)) . "-" . date('H:i', strtotime($fixedConflict->end_time))
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi otomatis ditolak karena bentrok dengan jadwal tetap',
                    'data' => new ReservationResource($reservation->load(['user', 'room']))
                ], 400);
            }

            // CEK 2: Reservation Conflict dengan yang sudah approved
            $reservationConflict = Reservations::where('room_id', $validated['room_id'])
                ->where('date', $validated['date'])
                ->where('status', 'approved')
                ->whereRaw('? < end_time AND ? > start_time', [
                    $validated['start_time'],
                    $validated['end_time']
                ])
                ->exists();

            if ($reservationConflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruangan sudah direservasi pada waktu tersebut'
                ], 400);
            }

            // Buat reservation dengan status pending
            // day_of_week akan auto-fill dari boot method
            $reservation = Reservations::create([
                'user_id' => Auth::id(),
                'room_id' => $validated['room_id'],
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'status' => 'pending'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil dibuat dan menunggu persetujuan',
                'data' => new ReservationResource($reservation->load(['user', 'room']))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat reservasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();

        $reservation = Reservations::with('room')->find($id);

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservasi tidak ditemukan'
            ], 404);
        }

        if ($user->hasRole('karyawan') && $reservation->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk melihat reservasi ini'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new ReservationResource($reservation)
        ], 200);
    }

    public function approve(Request $request, $id)
    {
        try {
            $request->validate([
                'reason' => 'nullable|string|max:255',
            ]);

            $reservation = Reservations::findOrFail($id);

            if (in_array($reservation->status, ['canceled', 'rejected'])) {
                return response()->json([
                    'message' => 'Reservasi yang sudah dibatalkan atau ditolak tidak bisa di-approve lagi.'
                ], 422);
            }

            $reservation->update([
                'status' => 'approved',
                'reason' => $request->input('reason'),
            ]);

            $room = Rooms::find($reservation->room_id);
            if ($room) {
                $room->update(['status' => 'active']);
            }

            Reservations::where('room_id', $reservation->room_id)
                ->where('id', '!=', $reservation->id)
                ->where('status', 'pending')
                ->where(function ($query) use ($reservation) {
                    $query->whereBetween('start_time', [$reservation->start_time, $reservation->end_time])
                        ->orWhereBetween('end_time', [$reservation->start_time, $reservation->end_time])
                        ->orWhere(function ($q) use ($reservation) {
                            $q->where('start_time', '<=', $reservation->start_time)
                                ->where('end_time', '>=', $reservation->end_time);
                        });
                })
                ->update([
                    'status' => 'rejected',
                    'reason' => 'Ditolak otomatis karena jadwal sudah diambil reservasi lain.',
                ]);

            // Mail::to($reservation->user->email)->send(new ReservationNotificationMail($reservation, 'approved'));

            return response()->json([
                'success' => true,
                'mesage' => 'Reservasi berhasil di-approve.',
                'data' => new ReservationApprovalResource($reservation),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'gagal approve reservasi.',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $reservation = Reservations::findOrFail($id);

        $reservation->update([
            'status' => 'rejected',
            'reason' => $request->reason,
        ]);

        // Mail::to($reservation->user->email)->send(new ReservationNotificationMail($reservation, 'rejected'));

        $room = Rooms::find($reservation->room_id);
        if ($room) {
            $room->update(['status' => 'inactive',]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reservasi berhasil ditolak.',
            'data' => new ReservationApprovalResource($reservation),
        ], 200);
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
        // Mail::to('admin@example.com')->send(new ReservationNotificationMail($reservation, 'canceled'));

        return response()->json([
            'success' => true,
            'message' => 'Reservasi berhasil dibatalkan.',
            'data' => new ReservationApprovalResource($reservation),
        ], 200);
    }
}
