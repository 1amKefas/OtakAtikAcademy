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
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('session_type', ['online', 'offline', 'hybrid'])->default('online');
            $table->enum('meeting_type', ['zoom', 'tatap_muka', 'other'])->default('other');
            $table->dateTime('session_date');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('zoom_link')->nullable(); // For Zoom meetings
            $table->string('location')->nullable(); // For offline/hybrid classes
            $table->text('offline_notes')->nullable(); // Additional offline class info
            $table->string('room_number')->nullable();
            $table->text('agenda')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
