<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SekolahApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// ✅ HANYA PERLU BLOK INI UNTUK FITUR SEKOLAH
Route::prefix('sekolah')->name('api.sekolah.')->group(function () {
    
    // Route ini satu-satunya yang dibutuhkan untuk dropdown pencarian Select2
    Route::get('/search', [SekolahApiController::class, 'search'])->name('search');

});