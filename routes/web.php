<?php

use App\Http\Controllers\Api\Admin\ReservationApprovalController;
use App\Mail\ReservationNotificationMail;
use App\Models\Reservations;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/preview-mail/{type}/{id}', function ($type, $id) {
    $reservation = Reservations::findOrFail($id);

    return new ReservationNotificationMail($reservation, $type);
});

Route::prefix('admin/reservations')->middleware(['auth', 'role:admin'])->group(function () {
    Route::post('{id}/approve', [ReservationApprovalController::class, 'approve'])->name('reservations.approve');
    Route::post('{id}/reject', [ReservationApprovalController::class, 'reject'])->name('reservations.reject');
});
