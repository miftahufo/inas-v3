<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unregistered_uids', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique(); // Kolom Kunci: UID yang belum terdaftar
            $table->dateTime('first_seen_at')->nullable(); // Waktu pertama kali tercatat
            $table->integer('tap_count')->default(1); // Jumlah tap kartu
            $table->timestamps(); // created_at & updated_at
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unregistered_uids');
    }
};
