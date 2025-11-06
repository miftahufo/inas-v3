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
        Schema::table('attendances', function (Blueprint $table) {
            // Menghapus UID lama dan menambahkan student_id (kunci asing)
            // Lakukan ini HANYA JIKA Anda sudah menjalankan query untuk membersihkan data duplikat UID.
            $table->unsignedBigInteger('student_id')->nullable()->after('uid');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('set null');
            
            // Hapus kolom created_at dan updated_at lama (jika ada) dan ganti dengan yang baru
            if (Schema::hasColumn('attendances', 'created_at')) {
                $table->dropColumn(['created_at', 'updated_at']);
            }
            
            // Kolom Status dan Waktu
            $table->dateTime('check_in_time')->nullable();
            $table->dateTime('check_out_time')->nullable();
            $table->string('status')->default('Masuk'); // Masuk, Pulang, Sakit, Ijin
            $table->text('keterangan')->nullable();
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->dropColumn(['student_id', 'check_in_time', 'check_out_time', 'status', 'keterangan']);
            // Tambahkan kembali kolom lama jika diperlukan
        });
    }
};
