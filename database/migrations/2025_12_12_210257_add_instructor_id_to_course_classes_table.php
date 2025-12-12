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
        Schema::table('course_classes', function (Blueprint $table) {
        // Tambahkan kolom instructor_id (PJ Kelas) setelah course_id
        if (!Schema::hasColumn('course_classes', 'instructor_id')) {
            $table->foreignId('instructor_id')
                  ->nullable() // Boleh kosong kalau belum ada PJ
                  ->after('course_id')
                  ->constrained('users') // Relasi ke tabel users
                  ->onDelete('set null');
        }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn('instructor_id');
        });;
    }
};
