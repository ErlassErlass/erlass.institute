<?php

use App\Http\Controllers\Api\SekolahApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ✅ API Sekolah — rate limited (60 req/min)
Route::middleware('throttle:60,1')->prefix('sekolah')->name('api.sekolah.')->group(function () {

    // Route ini satu-satunya yang dibutuhkan untuk dropdown pencarian Select2
    Route::get('/search', [SekolahApiController::class, 'search'])->name('search');

});

