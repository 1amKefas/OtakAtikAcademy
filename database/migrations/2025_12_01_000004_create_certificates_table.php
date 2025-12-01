<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Certificate template master data
        if (!Schema::hasTable('certificate_templates')) {
            Schema::create('certificate_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('background_image_path');
                $table->json('placeholders'); // e.g., {"student_name": {x: 100, y: 200}, "course_name": {...}}
                $table->string('signature_image_path')->nullable();
                $table->string('issuer_name');
                $table->string('issuer_title');
                $table->timestamps();
            });
        }

        // Generated certificates
        if (!Schema::hasTable('certificates')) {
            Schema::create('certificates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('course_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('certificate_template_id');
                $table->string('certificate_number')->unique();
                $table->string('pdf_file_path');
                $table->integer('course_hours');
                $table->date('issued_date');
                $table->timestamps();

                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('certificate_template_id')->references('id')->on('certificate_templates')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('certificate_templates');
    }
};
