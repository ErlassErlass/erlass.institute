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


// RUTE UNTUK DEPENDENT DROPDOWN
Route::prefix('sekolah')->name('api.sekolah.')->group(function () {
    Route::get('/kotkab-tipe', [SekolahApiController::class, 'getKotkabTipe'])->name('kotkab-tipe');
    Route::get('/kota', [SekolahApiController::class, 'getKota'])->name('kota');
    Route::get('/kecamatan', [SekolahApiController::class, 'getKecamatan'])->name('kecamatan');
    Route::get('/schools', [SekolahApiController::class, 'getSekolah'])->name('schools');
});