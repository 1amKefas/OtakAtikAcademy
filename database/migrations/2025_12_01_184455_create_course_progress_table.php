<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('course_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_module_id')->constrained()->onDelete('cascade');
            // Polymorphic relation untuk support Materi & Quiz
            $table->unsignedBigInteger('content_id'); 
            $table->string('content_type'); // 'material' atau 'quiz'
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Index biar query cepat
            $table->index(['user_id', 'course_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('course_progress');
    }
};