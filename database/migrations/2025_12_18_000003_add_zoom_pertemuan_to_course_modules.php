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
        Schema::table('course_modules', function (Blueprint $table) {
            // Add fields for Zoom/Pertemuan module type
            $table->enum('module_type', ['regular', 'zoom_pertemuan'])->default('regular')->after('order');
            $table->string('zoom_link')->nullable()->after('module_type');
            $table->string('meeting_type')->nullable()->comment('zoom, tatap_muka, hybrid')->after('zoom_link');
            $table->dateTime('session_date')->nullable()->after('meeting_type');
            $table->time('start_time')->nullable()->after('session_date');
            $table->time('end_time')->nullable()->after('start_time');
            $table->string('location')->nullable()->comment('For offline/tatap muka')->after('end_time');
            $table->string('room_number')->nullable()->after('location');
            $table->text('offline_notes')->nullable()->after('room_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropColumn([
                'module_type',
                'zoom_link',
                'meeting_type',
                'session_date',
                'start_time',
                'end_time',
                'location',
                'room_number',
                'offline_notes'
            ]);
        });
    }
};
