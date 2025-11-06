<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\Attendance; // WAJIB DI-IMPORT

class Student extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi secara massal (melalui form atau create/update).
     * Tambahkan 'uid', 'nama_lengkap', 'kelas', dan 'alamat'.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uid',
        'nama_lengkap',
        'kelas',
        'alamat',
    ];

    public function currentAttendance()
    {
        // Mencari catatan absensi hari ini yang cocok dengan UID siswa
        $today = \Illuminate\Support\Carbon::today()->toDateString(); 
        return $this->hasOne(\App\Models\Attendance::class, 'uid', 'uid')
                ->whereDate('check_in_time', $today)
                ->orderByDesc('check_in_time');
    }

    public function currentCheckIn()
    {
        $today = Carbon::today()->toDateString(); 
        return $this->hasOne(Attendance::class, 'uid', 'uid')
                    ->where('status', 'Masuk') // Hanya cari record berstatus MASUK
                    ->whereDate('check_in_time', $today)
                    ->latest('check_in_time'); // Ambil yang paling baru
    }

    public function currentCheckOut()
    {
        $today = Carbon::today()->toDateString(); 
        return $this->hasOne(Attendance::class, 'uid', 'uid')
                    ->where('status', 'Pulang') // Hanya cari record berstatus PULANG
                    ->whereDate('check_in_time', $today)
                    ->latest('check_in_time'); 
    }

    // Dipanggil oleh AbsensTable untuk kolom "Jam Masuk"
    public function getJamMasukAttribute() // Mengganti getCurrentCheckInTime()
    {
        // Mengambil waktu dari record yang berstatus 'Masuk'
        return $this->currentCheckIn?->check_in_time;
    }

    // Getter untuk mendapatkan waktu Pulang
    public function getJamPulangAttribute() // Mengganti getCurrentCheckOutTime()
    {
        // Mengambil waktu dari record yang berstatus 'Pulang'
        return $this->currentCheckOut?->check_in_time;
    }

    // Getter untuk mendapatkan Foto (diambil dari record Masuk)
    public function getCurrentPhotoPathAttribute()
    {
        // 1. Prioritas: Cek apakah record Pulang (currentCheckOut) ada
        if ($this->currentCheckOut) {
            return $this->currentCheckOut->image_path;
        }
        // 2. Jika Pulang tidak ada, cek apakah record Masuk (currentCheckIn) ada
        if ($this->currentCheckIn) {
            return $this->currentCheckIn->image_path;
        }
        // 3. Jika tidak ada keduanya, kembalikan null
        return null;
    }

    // ... (metode atau relasi lain di bawah ini, jika ada)
}
