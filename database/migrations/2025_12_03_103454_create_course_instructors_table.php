<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_instructors', function (Blueprint $table) {
            $table->id();
            // Kunci asing ke tabel courses
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            // Kunci asing ke tabel users (instruktur)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            
            // Kolom untuk menentukan Instruktur Utama
            $table->boolean('is_primary')->default(false); 
            // Kolom untuk pengurutan instruktur tambahan (jika diperlukan)
            $table->unsignedSmallInteger('sort_order')->default(99); 
            
            $table->timestamps();

            // Memastikan pasangan course_id dan user_id adalah unik
            $table->unique(['course_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_instructors');
    }
};