<?php

namespace App\Filament\Resources\Absens;

use App\Filament\Resources\Absens\Pages\CreateAbsen;
use App\Filament\Resources\Absens\Pages\EditAbsen;
use App\Filament\Resources\Absens\Pages\ListAbsens;
use App\Filament\Resources\Absens\Schemas\AbsenForm;
use App\Filament\Resources\Absens\Tables\AbsensTable;
use App\Models\Absen;
use App\Models\Student;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AbsenResource extends Resource
{
    protected static ?string $navigationLabel = 'Absen Harian'; // Atau 'Absen'
    protected static ?string $model = Student::class;
    
   
    
    // Pastikan properti ini ada (jika Anda ingin mengontrol urutan)
    protected static ?int $navigationSort = 1; // Atur ke angka rendah agar muncul di atas

    //protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    public static function form(Schema $schema): Schema
    {
        return AbsenForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbsensTable::configure($table);
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
            'index' => ListAbsens::route('/'),
            // 'create' => CreateAbsen::route('/create'),
            // 'edit' => EditAbsen::route('/{record}/edit'),
        ];
    }
}
