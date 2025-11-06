<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class StudentForm
{
    public static function make(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('uid')
                ->label('UID')
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('nama_lengkap')
                ->label('Nama Lengkap')
                ->required(),

            TextInput::make('kelas')
                ->label('Kelas')
                ->required(),

            Textarea::make('alamat')
                ->label('Alamat')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }
}
