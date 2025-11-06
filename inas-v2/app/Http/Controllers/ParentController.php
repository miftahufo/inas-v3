<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ParentController extends Controller
{
    
    private function cleanImagePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }
        // Menghapus 'storage/' jika ada di awal path (kasus data lama)
        // Kita menggunakan str_replace karena beberapa data lama mungkin sudah terlanjur tersimpan.
        return str_replace('storage/images/', 'images/', $path);
    }
    
    public function showAttendance($uid)
    {
        // 1. Cari Siswa berdasarkan UID
        $student = Student::where('uid', $uid)->first();

        if (!$student) {
            return view('parents.attendance-view', ['error' => 'UID siswa tidak valid atau tidak terdaftar.']);
        }

        // 2. Ambil record Masuk dan Pulang (Relasi terpisah yang sama dengan di Model Student)
        // Kita menggunakan relasi yang sudah didefinisikan di Model Student
        $checkInRecord = $student->currentCheckIn()->first();
        $checkOutRecord = $student->currentCheckOut()->first();
        
        // 3. Tentukan Status dan Foto (Logika Baru)
        $data = [
            'nama' => $student->nama_lengkap,
            'kelas' => $student->kelas,
            'status_kehadiran' => 'Belum Absen',
            'jam_masuk' => null,
            'jam_pulang' => null,
            'foto_url' => null,
        ];

        if ($checkInRecord) {
            $pathMasuk = $this->cleanImagePath($checkInRecord->image_path);
            // Jam Masuk selalu diambil dari record Check-in
            $data['jam_masuk'] = Carbon::parse($checkInRecord->check_in_time)->format('H:i:s, d M Y');
            $data['status_kehadiran'] = 'Masuk';
            $data['foto_url'] = asset('storage/' . $pathMasuk);}
        
        if ($checkOutRecord) {
            $pathPulang = $this->cleanImagePath($checkOutRecord->image_path);
            // Jam Pulang
            $data['jam_pulang'] = Carbon::parse($checkOutRecord->check_in_time)->format('H:i:s');
            $data['status_kehadiran'] = 'Pulang'; // Status terakhir adalah Pulang
            // Foto terakhir (yang ditampilkan) adalah foto Pulang
            $data['foto_url'] = asset('storage/' . $pathPulang); 
        }


        // 4. Render Tampilan Web
        return view('parents.attendance-view', ['data' => $data]);
    }
}