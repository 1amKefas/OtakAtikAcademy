<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_classes', function (Blueprint $table) {
            // 1. Rename class_name jadi name (biar sesuai Controller)
            if (Schema::hasColumn('course_classes', 'class_name') && !Schema::hasColumn('course_classes', 'name')) {
                $table->renameColumn('class_name', 'name');
            }

            // 2. Tambah kolom slug (karena Controller pake Str::slug)
            if (!Schema::hasColumn('course_classes', 'slug')) {
                $table->string('slug')->nullable()->after('id');
            }

            // 3. Make schedule columns nullable (biar gak error kalau kosong saat create)
            // Karena di Controller lo gak ngisi start_date/end_date saat create
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
            $table->time('start_time')->nullable()->change();
            $table->time('end_time')->nullable()->change();
            $table->string('days_of_week')->nullable()->change();
            
            // 4. Tambah deskripsi & lokasi jika belum ada
            if (!Schema::hasColumn('course_classes', 'schedule_description')) {
                $table->string('schedule_description')->nullable();
            }
            if (!Schema::hasColumn('course_classes', 'room_location')) {
                $table->string('room_location')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('course_classes', function (Blueprint $table) {
            // Balikin kalau rollback
            if (Schema::hasColumn('course_classes', 'name')) {
                $table->renameColumn('name', 'class_name');
            }
            if (Schema::hasColumn('course_classes', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};