<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add course_type and course_duration columns to courses table
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('course_type', ['online', 'offline', 'hybrid'])->default('online')->after('is_active');
            $table->integer('duration_days')->nullable()->after('course_type');
            $table->unsignedInteger('rating_count')->default(0)->after('duration_days');
            $table->decimal('average_rating', 3, 2)->default(0)->after('rating_count');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['course_type', 'duration_days', 'rating_count', 'average_rating']);
        });
    }
};
