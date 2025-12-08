<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update tabel course_classes (tambah pengajar khusus kelas)
        Schema::table('course_classes', function (Blueprint $table) {
            if (!Schema::hasColumn('course_classes', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete()
                    ->after('course_id')
                    ->comment('Assistant/Instructor khusus untuk kelas ini');
            }
            // Tambah kuota per kelas jika belum ada
            if (!Schema::hasColumn('course_classes', 'quota')) {
                $table->integer('quota')->default(30)->after('name');
            }
        });

        // 2. Update tabel course_registrations (mapping student ke kelas)
        Schema::table('course_registrations', function (Blueprint $table) {
            if (!Schema::hasColumn('course_registrations', 'course_class_id')) {
                $table->foreignId('course_class_id')->nullable()->constrained('course_classes')->nullOnDelete()
                    ->after('course_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropForeign(['course_class_id']);
            $table->dropColumn('course_class_id');
        });

        Schema::table('course_classes', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropColumn(['instructor_id', 'quota']);
        });
    }
};