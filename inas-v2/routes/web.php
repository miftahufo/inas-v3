<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ParentController;
// Tambahkan middleware 'api' untuk rute ini


Route::get('/', function () {
    return redirect()->to('/admin');
});

Route::get('/api/test', function () {
    return 'Laravel WEB API is now working!';
});

Route::middleware('web')->group(function () {
    Route::get('/api/absensi-anak/{uid}', [App\Http\Controllers\ParentController::class, 'showAttendance'])->name('parent.attendance');
});


Route::post('/absen', [ApiController::class, 'store']);

