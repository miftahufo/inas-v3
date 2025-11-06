<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menghapus index unik dari kolom 'uid'
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique(['uid']);
        });
    }

    public function down(): void
    {
        // Menambahkan kembali index unik di fungsi 'down' untuk rollback
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('uid')->unique()->change();
        });
    }
};
