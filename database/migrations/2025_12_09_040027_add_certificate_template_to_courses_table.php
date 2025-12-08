<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // Kolom untuk menyimpan path file gambar template sertifikat
            $table->string('certificate_template')->nullable()->after('image_url');
            // Posisi nama (X, Y) opsional, kalau mau advanced nanti
            // $table->integer('cert_name_x')->default(400);
            // $table->integer('cert_name_y')->default(300);
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('certificate_template');
        });
    }
};