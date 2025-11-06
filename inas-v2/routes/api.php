<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Attendance;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\Controller;

Route::get('/absensi-anak/{uid}', [ParentController::class, 'showAttendance'])->name('parent.attendance');

Route::post('/attendances', function (Request $request) {
    $request->validate([
        'uid' => 'required|string',
        'image_file' => 'required|file|mimes:jpeg,png,jpg|max:2048',
    ]);

    // Simpan file gambar ke storage/app/public/attendances
    $path = $request->file('image_file')->store('attendances', 'public');

    // Simpan ke database
    $attendance = Attendance::create([
        'uid' => $request->uid,
        'image_path' => $path,
    ]);
    
    return response()->json([
        'success' => true,
        'data' => $attendance,
    ]);
});

Route::get('/test', function () {
    return 'Laravel API is working!';
    });