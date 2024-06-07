<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("attendance")->group(function () {
    Route::get('/showSessionQr/{session}', [AttendanceController::class, 'showSessionQr']);
    Route::post('/scanQr', [AttendanceController::class, 'scanQr']);
});

Route::post('/register', [AttendanceController::class, 'register']);

Route::post('/login', [AttendanceController::class, 'login']);
