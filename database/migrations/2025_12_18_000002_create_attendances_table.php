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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_session_id')->nullable()->constrained('class_sessions')->onDelete('set null');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('absent');
            $table->timestamp('marked_at')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            
            // Unique constraint: satu user hanya bisa mark attendance sekali per session
            $table->unique(['user_id', 'class_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
