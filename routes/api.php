<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/signup', [AuthController::class, 'signup'])->name('api.signup');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/me', [AuthController::class, 'me'])->name('api.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');

    Route::get('/owner/ability-check', function (Request $request) {
        return response()->json([
            'message' => 'Owner ability granted.',
            'user_id' => $request->user()->id,
            'abilities' => $request->user()->currentAccessToken()?->abilities ?? [],
        ]);
    })->middleware('abilities:owner');

    Route::get('/contractor/ability-check', function (Request $request) {
        return response()->json([
            'message' => 'Contractor ability granted.',
            'user_id' => $request->user()->id,
            'abilities' => $request->user()->currentAccessToken()?->abilities ?? [],
        ]);
    })->middleware('abilities:contractor');
});
