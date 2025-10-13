<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\ProfileController;
use App\Http\Controllers\Api\FixedScheduleController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::post('/auth/login', [LoginController::class, 'login']);
Route::post('/auth/register', [RegisterController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    // Profile routes
    Route::get('auth/me', [ProfileController::class, 'profile']);
    Route::put('auth/me', [ProfileController::class, 'update']);
    Route::post('/auth/logout', [LogoutController::class, 'logout']);

    Route::get('rooms', [RoomController::class, 'index']);
    Route::get('rooms/{id}', [RoomController::class, 'show']);

    Route::get('fixed-schedules/{id}', [FixedScheduleController::class, 'show']);
    Route::get('fixed-schedules', [FixedScheduleController::class, 'index']);

    Route::get('reservations/{id}', [ReservationController::class, 'show']);
    Route::get('reservations', [ReservationController::class, 'index'])->name('index');


    Route::middleware('role_or_permission:admin|manage.users')->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{id}', [UserController::class, 'show']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);
    });

    Route::middleware('role_or_permission:admin|manage rooms')->group(function () {
        Route::post('rooms', [RoomController::class, 'store']);
        Route::put('rooms/{id}', [RoomController::class, 'update']);
        Route::delete('rooms/{id}', [RoomController::class, 'destroy']);
    });

    Route::middleware('role_or_permission:admin|manage.schedule')->group(function () {
        Route::post('fixed-schedules', [FixedScheduleController::class, 'store']);
        Route::put('fixed-schedules/{id}', [FixedScheduleController::class, 'update']);
        Route::delete('fixed-schedules/{id}', [FixedScheduleController::class, 'destroy']);
    });

    Route::middleware('role_or_permission:admin|approve reservation')->group(function () {
        Route::patch('reservations/{id}/approve', [ReservationController::class, 'approve']);
    });

    Route::middleware('role_or_permission:admin|reject reservation')->group(function () {
        Route::patch('reservations/{id}/reject', [ReservationController::class, 'reject']);
    });

    Route::middleware('role_or_permission:karyawan|request reservation')->group(function () {
        Route::post('reservations', [ReservationController::class, 'store']);
    });

    Route::middleware('role_or_permission:karyawan|view reservation')->group(function () {});

    Route::middleware('role_or_permission:karyawan|cancel own reservation')->group(function () {
        Route::patch('reservations/{id}/cancel', [ReservationController::class, 'cancel']);
    });
});
