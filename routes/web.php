<?php

use App\Mail\ReservationNotificationMail;
use App\Models\Reservations;
use Illuminate\Support\Facades\Route;

Route::view('/{any}', 'app')->where('any', '.*');


Route::get('/preview-mail/{type}/{id}', function ($type, $id) {
    $reservation = Reservations::findOrFail($id);

    return new ReservationNotificationMail($reservation, $type);
});
