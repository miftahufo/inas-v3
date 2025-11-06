<?php

namespace App\Filament\Resources\Absens\Tables;

use App\Models\Student;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class AbsensTable
{
    public static function configure(Table $table): Table
    {
        // Mendapatkan waktu server saat ini (Dipertahankan)
        $currentTime = Carbon::now('Asia/Jakarta');
        $noon = Carbon::today('Asia/Jakarta')->setTime(12, 0, 0);

        return $table
            ->columns([
                
                TextColumn::make('nama_lengkap')->label('Nama Siswa')->searchable(),
                TextColumn::make('kelas')->label('Kelas')->sortable(),

                // 1. KOLOM JAM MASUK (DIGANTI MENGGUNAKAN ACCESSOR jam_masuk)
                TextColumn::make('jam_masuk')
                    // Mengambil nilai dari public function getJamMasukAttribute() di Model Student
                    ->getStateUsing(fn (Student $record) => $record->jam_masuk) 
                    ->dateTime('d-m-Y H:i:s')
                    ->label('Jam Masuk')
                    ->placeholder('BELUM HADIR')
                    ->sortable(),

                // 2. KOLOM JAM PULANG (DIGANTI MENGGUNAKAN ACCESSOR jam_pulang)
                TextColumn::make('jam_pulang')
                    // Mengambil nilai dari public function getJamPulangAttribute() di Model Student
                    ->getStateUsing(fn (Student $record) => $record->jam_pulang)
                    ->dateTime('d-m-Y H:i:s')
                    ->label('Jam Pulang')
                    ->placeholder('BELUM PULANG')
                    ->sortable(),
                    
                // 3. KOLOM FOTO MASUK
                ImageColumn::make('foto')
                ->getStateUsing(function (Student $record) use ($currentTime, $noon) {
                    
                    // Logika Jam 12 Siang (Dipertahankan jika Anda memang memerlukannya)
                    if ($currentTime->greaterThanOrEqualTo($noon)) {
                        // Jika Anda ingin foto hilang setelah jam 12, aktifkan ini. 
                        // Jika tidak, hapus blok if ini.
                        // return null; 
                    }
                        
                    // KUNCI PERBAIKAN: Mengambil path melalui Accessor gabungan di Model
                    $finalPath = $record->current_photo_path; 
                    
                    // Path di DB Anda adalah: storage/images/...
                    return $finalPath ? asset($finalPath) : null; 
                })
                // URL juga menggunakan Accessor gabungan agar foto yang diklik sesuai dengan yang ditampilkan
                ->url(fn (Student $record) => $record->current_photo_path ? asset($record->current_photo_path) : null) 
                ->openUrlInNewTab()
                ->disk('public') // Pastikan disk ini benar
                ->size(80) 
                ->label('Foto')
                ->placeholder('-'),
                
                TextColumn::make('uid')->label('UID Kartu')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('nama_lengkap', 'asc');
    }
}