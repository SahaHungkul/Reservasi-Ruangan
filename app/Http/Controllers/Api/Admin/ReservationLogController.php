<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ReservationLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query()
            ->where('log_name', 'reservation')
            ->with('causer'); // memuat user yang melakukan

        if ($request->filled('action')) {
            // action: created, updated, deleted, atau log message text
            $query->where('description', 'like', "%{$request->action}%");
        }

        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        $logs = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
