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
        // Achievements table (badge definitions)
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // first_course, five_courses, etc
            $table->string('name_en');
            $table->string('name_id');
            $table->text('description_en');
            $table->text('description_id');
            $table->string('icon'); // SVG path or icon class
            $table->string('color'); // Tailwind color: bg-blue-100, text-blue-600
            $table->integer('requirement_type'); // 1=courses_completed, 2=hours_learned, 3=quiz_score
            $table->integer('requirement_value'); // 1, 5, 10, 50, 100 etc
            $table->timestamps();
        });

        // User achievements (earned badges)
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained()->onDelete('cascade');
            $table->timestamp('earned_at');
            $table->timestamps();
            $table->unique(['user_id', 'achievement_id']);
        });

        // User certificates
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('certificate_number')->unique();
            $table->date('issued_date');
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('achievements');
    }
};
