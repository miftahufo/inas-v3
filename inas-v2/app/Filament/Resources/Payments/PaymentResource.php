<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel students
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            
            $table->decimal('amount', 15, 2); // Jumlah uang (maks 15 digit, 2 desimal)
            $table->string('payment_type')->default('SPP'); // Jenis: SPP, Gedung, Seragam
            $table->date('payment_date'); // Tanggal pembayaran
            $table->text('notes')->nullable(); // Catatan tambahan
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};