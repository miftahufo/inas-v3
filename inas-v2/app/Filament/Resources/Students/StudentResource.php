<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Models\Student;

// Import Inti Filament
use Filament\Schemas\Schema; // Menggantikan Form::class (untuk F4)
use Filament\Resources\Resource;
use Filament\Tables\Table;

// Component & Columns
use Filament\Forms\Components\TextInput; 
use Filament\Tables\Columns\TextColumn;

// Actions
use Filament\Actions\CreateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;


class StudentResource extends Resource
{
    protected static ?string $model = Student::class;
    
    //FIX P1077: Menggunakan deklarasi ?string saja. Ini yang paling fungsional
    //protected static ?string $navigationIcon = 'heroicon-o-user-group'; 
    
    protected static ?string $recordTitleAttribute = 'uid';
    
    public static function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('uid')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('UID Kartu RFID'),

                TextInput::make('nama_lengkap')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Lengkap'),

                TextInput::make('kelas')
                    ->maxLength(255),
                    
                TextInput::make('alamat')
                    ->maxLength(255)
                    ->columnSpanFull(), 
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uid')->searchable()->sortable(),
                TextColumn::make('nama_lengkap')->searchable()->sortable(),
                TextColumn::make('kelas')->searchable()->sortable(),
                TextColumn::make('alamat')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
        ->actions([ 
            // EDIT ACTION DIKEMBALIKAN (UNCOMMENT)
            EditAction::make(),
        ])
        ->bulkActions([
            // BULK ACTIONS DIKEMBALIKAN (UNCOMMENT)
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getHeaderActions(): array
    {
        return [
            'index' => ListStudents::route('/')
        ];
    }
}