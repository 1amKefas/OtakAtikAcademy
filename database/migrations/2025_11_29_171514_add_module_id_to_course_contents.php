<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tambahkan kolom course_module_id ke tabel course_materials
        Schema::table('course_materials', function (Blueprint $table) {
            // 'after' opsional, cuma biar rapi urutan kolomnya
            $table->foreignId('course_module_id')
                  ->nullable()
                  ->after('course_id')
                  ->constrained('course_modules')
                  ->onDelete('cascade');
                  
            // Sekalian tambah tipe konten jika belum ada (file/video/link)
            if (!Schema::hasColumn('course_materials', 'type')) {
                $table->string('type')->default('file')->after('title'); 
            }
            if (!Schema::hasColumn('course_materials', 'external_url')) {
                $table->string('external_url')->nullable()->after('file_path');
            }
        });

        // Tambahkan kolom course_module_id ke tabel course_assignments
        Schema::table('course_assignments', function (Blueprint $table) {
            $table->foreignId('course_module_id')
                  ->nullable()
                  ->after('course_id')
                  ->constrained('course_modules')
                  ->onDelete('cascade');
        });

        // Tambahkan kolom course_module_id ke tabel quizzes
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('course_module_id')
                  ->nullable()
                  ->after('course_id')
                  ->constrained('course_modules')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        // Logic rollback (hapus kolom kalau migrate:rollback)
        Schema::table('course_materials', function (Blueprint $table) {
            $table->dropForeign(['course_module_id']);
            $table->dropColumn(['course_module_id', 'type', 'external_url']);
        });

        Schema::table('course_assignments', function (Blueprint $table) {
            $table->dropForeign(['course_module_id']);
            $table->dropColumn('course_module_id');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['course_module_id']);
            $table->dropColumn('course_module_id');
        });
    }
};