<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Permintaan API diterima. Memproses data...');
        
        try {
            // Periksa apakah UID dan file gambar ada di permintaan
            if (!$request->has('uid') || !$request->hasFile('image_file')) {
                Log::error('Validasi gagal: UID atau file gambar tidak ada.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'Missing UID or image data'
                ], 400);
            }

            $uid = $request->input('uid');
            $imageFile = $request->file('image_file');

            // Simpan gambar ke folder storage
            $filename = Str::slug($uid) . '_' . now()->format('YmdHis') . '.jpg';
            $path = $imageFile->storeAs('public/attendances', $filename);
            $imageUrl = Storage::url($path);
            
            // Simpan data ke database
            $attendance = new Attendance();
            $attendance->uid = $uid;
            $attendance->image_path = $imageUrl;
            $attendance->save();

            Log::info('Data absensi berhasil disimpan ke database.');
            
            return response()->json([
                'status' => 'success',
                'message' => 'Attendance recorded successfully',
                'data' => [
                    'uid' => $uid,
                    'image_url' => $imageUrl
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error saat memproses permintaan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Server error occurred.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
}