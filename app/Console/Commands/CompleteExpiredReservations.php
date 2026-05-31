<?php

namespace App\Console\Commands;

use App\Models\FixedSchedule;
use App\Models\Reservations;
use App\Models\Rooms;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CompleteExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:complete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status reservasi expired dan sinkronisasi status ruangan';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->completeExpiredReservations();
        $this->syncRoomStatus();
    }

    private function completeExpiredReservations() {
        $now = now();

        $updated = Reservations::where('status', 'approved')
            ->where(function ($query) use ($now) {
                $query->whereDate('date', '<', $now->toDateString())
                      ->orWhere(function ($q) use ($now) {
                          $q->whereDate('date', $now->toDateString())
                            ->where('end_time', '<=', $now->format('H:i:s'));
                      });
            })
            ->update(['status' => 'completed']);

        Log::info("[Scheduler] {$updated} reservasi ditandai completed.");
        $this->info("{$updated} reservasi → completed.");
    }

    private function syncRoomStatus(): void
    {
        $now        = now();
        $dayOfWeek  = strtolower($now->format('l'));
        $currentTime = $now->format('H:i:s');
        $today      = $now->toDateString();

        // 1. Room aktif karena Fixed Schedule (prioritas utama)
        $fixedActiveRoomIds = FixedSchedule::where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>', $currentTime)
            ->pluck('room_id')
            ->unique();

        // 2. Room aktif karena Reservasi approved (prioritas kedua)
        $reservationActiveRoomIds = Reservations::where('status', 'approved')
            ->where('date', $today)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>', $currentTime)
            ->pluck('room_id')
            ->unique();

        // Gabungkan keduanya — fixed schedule otomatis lebih prioritas
        // karena reservasi yang bentrok sudah ditolak sejak awal
        $allActiveRoomIds = $fixedActiveRoomIds->merge($reservationActiveRoomIds)->unique();

        // Set active
        $activated = Rooms::whereIn('id', $allActiveRoomIds)
            ->where('status', 'inactive')
            ->update(['status' => 'active']);

        // Set inactive
        $deactivated = Rooms::whereNotIn('id', $allActiveRoomIds)
            ->where('status', 'active')
            ->update(['status' => 'inactive']);

        Log::info("[Scheduler] Room sync: {$activated} → active, {$deactivated} → inactive.");
        $this->info("Room sync: {$activated} → active, {$deactivated} → inactive.");
    }
}
