<?php

namespace App\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str; 
use Filament\Tables\Table;

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

                TextColumn::make('created_at')
                    ->label('Waktu Absen')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable(),
                    ])
                    ->filters([
                        //
                    ]);
                    // ->recordActions([
                    //     EditAction::make(),
                    // ])
                    // ->toolbarActions([
                    //     BulkActionGroup::make([
                    //         DeleteBulkAction::make(),
                    //     ]),
                    // ]);
    }
}
