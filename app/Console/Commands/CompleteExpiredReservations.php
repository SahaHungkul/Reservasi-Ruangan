<?php

namespace App\Console\Commands;

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

    private function syncRoomStatus() {
        $now = now();

        // Ambil room_id yang sedang aktif (ada reservasi berjalan sekarang)
        $activeRoomIds = Reservations::where('status', 'approved')
            ->whereDate('date', $now->toDateString())
            ->where('start_time', '<=', $now->format('H:i:s'))
            ->where('end_time', '>', $now->format('H:i:s'))
            ->pluck('room_id')
            ->unique();

        // Set room yang sedang ada reservasi berjalan → active
        $activated = Rooms::whereIn('id', $activeRoomIds)
            ->where('status', 'inactive')
            ->update(['status' => 'active']);

        // Set room yang tidak ada reservasi berjalan → inactive
        $deactivated = Rooms::whereNotIn('id', $activeRoomIds)
            ->where('status', 'active')
            ->update(['status' => 'inactive']);

        Log::info("[Scheduler] Room sync: {$activated} → active, {$deactivated} → inactive.");
        $this->info("Room sync: {$activated} → active, {$deactivated} → inactive.");
    }
}
