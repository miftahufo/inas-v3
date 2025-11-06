<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'uid',
        'foto',
        'waktu_absen',
    ];

    public function getFotoUrlAttribute()
    {
        return $this->foto 
            ? asset('storage/' . $this->foto)
            : null;
    }
    public function student()
    {
        // Asumsi Anda menggunakan student_id sebagai foreign key yang merujuk ke id di tabel students
        return $this->belongsTo(Student::class, 'uid', 'uid');
    }
}
