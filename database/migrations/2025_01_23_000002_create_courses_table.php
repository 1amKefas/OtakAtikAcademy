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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['Full Online', 'Hybrid', 'Tatap Muka']);
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('price', 10, 2);
            $table->string('discount_code')->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->integer('min_quota');
            $table->integer('max_quota');
            $table->integer('current_enrollment')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};