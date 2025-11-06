<?php

namespace App\Filament\Resources\Attendances\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uid')
                    ->label('UID')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('image_path')
                    ->label('Foto')
                    ->image()
                    ->directory('attendances')
                    ->disk('public')
                    ->required(),
            ]);
    }
}
