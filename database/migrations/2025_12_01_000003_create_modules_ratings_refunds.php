<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Course modules/topics system
        if (!Schema::hasTable('course_modules')) {
            Schema::create('course_modules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            });
        }

        // Module materials (video, file, gdrive link, etc)
        if (!Schema::hasTable('module_materials')) {
            Schema::create('module_materials', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('module_id');
                $table->string('title');
                $table->text('description')->nullable();
                $table->enum('type', ['video', 'file', 'image', 'link', 'gdrive', 'text'])->default('file');
                $table->string('file_url')->nullable(); // for file/video/image
                $table->string('external_url')->nullable(); // for gdrive/link
                $table->unsignedInteger('duration_minutes')->nullable(); // for video
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('module_id')->references('id')->on('course_modules')->onDelete('cascade');
            });
        }

        // Instructor - Course relationship (many-to-many)
        if (!Schema::hasTable('course_instructor')) {
            Schema::create('course_instructor', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id');
                $table->unsignedBigInteger('instructor_id');
                $table->integer('active_duration_days')->nullable(); // how long instructor is active
                $table->string('zoom_link')->nullable(); // for hybrid courses
                $table->timestamp('zoom_start_time')->nullable(); // when zoom session starts
                $table->text('notes')->nullable(); // additional notes/tasks
                $table->timestamps();

                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['course_id', 'instructor_id']);
            });
        }

        // Rating system
        if (!Schema::hasTable('course_ratings')) {
            Schema::create('course_ratings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedTinyInteger('rating')->min(1)->max(5);
                $table->text('review')->nullable();
                $table->timestamps();

                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['course_id', 'user_id']);
            });
        }

        // Refund tracking with status
        if (!Schema::hasTable('refunds')) {
            Schema::create('refunds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('user_id');
                $table->decimal('refund_amount', 10, 2);
                $table->text('reason');
                $table->enum('status', ['unread', 'processing', 'approved', 'rejected'])->default('unread');
                $table->text('admin_notes')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('course_ratings');
        Schema::dropIfExists('course_instructor');
        Schema::dropIfExists('module_materials');
        Schema::dropIfExists('course_modules');
    }
};
