<?php

use App\Http\Controllers\Api\Admin\ReservationApprovalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\FixedScheduleController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\RoomController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/auth/login', [LoginController::class, 'login']);
Route::post('/auth/register', [RegisterController::class, 'register']);



Route::middleware('auth:api')->group(function () {
    Route::post('/auth/logout', [LogoutController::class, 'logout']);
    Route::get('auth/me', [ProfileController::class, 'profile']);

    Route::get('rooms', [RoomController::class,'index']);
    Route::post('rooms', [RoomController::class,'store']);
    Route::get('rooms/{id}', [RoomController::class,'show']);
    Route::put('rooms/{id}', [RoomController::class,'update']);
    Route::delete('rooms/{id}', [RoomController::class,'destroy']);


    Route::get('reservations', [ReservationController::class, 'index'])->name('index');
    Route::post('reservations', [ReservationController::class, 'store']);
    Route::get('reservations/{id}', [ReservationController::class, 'show']);
    Route::put('reservations/{id}', [ReservationController::class, 'update']);
    Route::delete('reservations/{id}', [ReservationController::class, 'destroy']);

    Route::patch('reservations/{id}/approve', [ReservationApprovalController::class,'approve']);
    Route::patch('reservations/{id}/reject', [ReservationApprovalController::class,'reject']);

    Route::get('fixed-schedules',[FixedScheduleController::class,'index']);
    Route::post('fixed-schedules',[FixedScheduleController::class,'store']);
    Route::get('fixed-schedules/{id}',[FixedScheduleController::class,'show']);
    Route::put('fixed-schedules/{id}',[FixedScheduleController::class,'update']);
    Route::delete('fixed-schedules/{id}',[FixedScheduleController::class,'destroy']);
});
