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
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('ttl'); // Tempat Tanggal Lahir
            $table->string('tempat_tinggal');
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->enum('course', ['Starter', 'Pro Learner', 'Expert Mode']);
            $table->string('sub_course1');
            $table->string('sub_course2');
            $table->string('kelas');
            $table->integer('price');
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_registrations');
    }
};