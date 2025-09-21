<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\FixedSchedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// $conflict = FixedSchedule::where('room_id', $request->room_id)
//     ->where('day_of_week', strtolower(\Carbon\Carbon::parse($request->start_time)->format('l')))
//     ->where(function ($q) use ($request) {
//         $q->whereBetween('start_time', [$request->start_time, $request->end_time])
//           ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
//           ->orWhere(function ($q2) use ($request) {
//               $q2->where('start_time', '<', $request->start_time)
//                  ->where('end_time', '>', $request->end_time);
//           });
//     })
//     ->exists();

// if ($conflict) {
//     return response()->json([
//         'message' => 'Ruangan sudah terjadwal rutin pada waktu tersebut'
//     ], 422);
// }
