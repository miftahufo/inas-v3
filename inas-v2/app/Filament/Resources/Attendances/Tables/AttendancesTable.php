<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str; 
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Kolom Nama Siswa (MENGAMBIL DARI RELASI student()->nama_lengkap)
            TextColumn::make('student.nama_lengkap')
                ->searchable()
                ->sortable()
                ->label('Nama Siswa'),

                // TextColumn::make('id')
                //     ->label('ID')
                //     ->sortable(),

                // TextColumn::make('uid')
                //     ->label('UID')
                //     ->searchable(),
                TextColumn::make('student.kelas')
                    ->label('Kelas')
                    ->sortable(),

                ImageColumn::make('image_path')
                    ->label('Foto')
                    ->getStateUsing(fn ($record) => asset($record->image_path))
                    ->square()
                    ->size(80)
                    ->url(fn ($record) => asset( $record->image_path)) // kasih URL besar
                    ->openUrlInNewTab(), // atau nanti bisa custom lightbox,

                
                
                    // 4. STATUS (Kolom Baru)
                    // Asumsi: Ada kolom 'status' di tabel attendances (misal: 'Hadir', 'Terlambat', 'Pulang')
                    // Jika belum ada di DB, kita bisa buat logika virtual based on jam
                    TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Hadir' => 'success',     // Hijau
                        'Pulang' => 'info',       // Biru
                        default => 'gray',
                    })
                    ->sortable(),
                    
                    TextColumn::make('created_at')
                    ->label('Waktu Absen')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable(),
                    ])
                    ->filters([
                        //
                    ])
                    // ->recordActions([
                    //     EditAction::make(),
                    // ])
                    ->toolbarActions([
                         BulkActionGroup::make([
                            BulkAction::make('exportPdf')
                                ->label('Export PDF')
                                ->icon('heroicon-o-printer')
                                ->action(function (Collection $records) {
                                    // Header HTML
                                    $html = '<h2 style="text-align:center;">Laporan Absensi Siswa</h2>';
                                    $html .= '<p>Tanggal Cetak: ' . date('d-m-Y H:i') . '</p>';
                                    $html .= '<table border="1" cellspacing="0" cellpadding="5" width="100%" style="border-collapse: collapse;">';
                                    $html .= '<thead style="background-color: #f2f2f2;"><tr>
                                                    <th style="width: 5%;">No</th>
                                                    <th style="width: 15%;">Foto</th> <th>Nama Siswa</th>
                                                    <th>Kelas</th>
                                                    <th>Waktu Absen</th>
                                                    <th>Status</th>
                                                </tr></thead>';
                                    $html .= '<tbody>';

                                    foreach ($records as $index => $record) {
                                        // Logika untuk Gambar
                                        // Mengubah path 'storage/images/...' menjadi path fisik absolut di server
                                        // Contoh hasil: C:\laragon\www\project\public\storage\images\foto.jpg
                                        $imagePath = public_path($record->image_path);
                                        
                                        // Cek apakah file ada di folder public untuk mencegah error
                                        if (File::exists($imagePath) && !empty($record->image_path)) {
                
                                            // --- KODE BARU: UBAH KE BASE64 ---
                                            try {
                                                // Ambil isi file gambar
                                                $imageData = \base64_encode(file_get_contents($imagePath));
                                                
                                                // Format src untuk HTML (data:image/jpeg;base64,...)
                                                $src = 'data:' . \mime_content_type($imagePath) . ';base64,' . $imageData;
                                                
                                                // Masukkan ke tag IMG
                                                $imgTag = '<img src="' . $src . '" width="50" height="50" style="object-fit: cover; border-radius: 5px;">';
                                            } catch (\Exception $e) {
                                                $imgTag = '<span style="color:red; font-size:10px;">Error Load</span>';
                                            }
                                            // ---------------------------------

                                        } else {
                                            $imgTag = '<span style="color:red; font-size:10px;">No Image</span>';
                                        }

                                        $html .= '<tr>';
                                        $html .= '<td style="text-align:center;">' . ($index + 1) . '</td>';
                                        $html .= '<td style="text-align:center;">' . $imgTag . '</td>'; // Masukkan kolom gambar di sini
                                        $html .= '<td>' . ($record->student->nama_lengkap ?? '-') . '</td>';
                                        $html .= '<td style="text-align:center;">' . ($record->student->kelas ?? '-') . '</td>';
                                        $html .= '<td style="text-align:center;">' . $record->created_at->format('d-m-Y H:i:s') . '</td>';
                                        $html .= '<td style="text-align:center;">' . ($record->status ?? '-') . '</td>';
                                        $html .= '</tr>';
                                    }

                                    $html .= '</tbody></table>';

                                    // Load PDF
                                    $pdf = Pdf::loadHTML($html)->setPaper('a4', 'portrait');
                                    
                                    // Penting: Izinkan DOMPDF mengakses folder remote/lokal jika masih gagal
                                    $pdf->setOptions(['isRemoteEnabled' => true]); 

                                    return response()->streamDownload(fn () => print($pdf->output()), 'laporan-absensi.pdf');
                                })
                                ->deselectRecordsAfterCompletion(),
                            //         DeleteBulkAction::make(),
                        ])
                    ]);
    }
}
