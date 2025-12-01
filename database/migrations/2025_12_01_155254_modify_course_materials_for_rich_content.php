<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('course_materials', function (Blueprint $table) {
            // Ubah description jadi LONGTEXT untuk menampung konten HTML panjang
            $table->longText('description')->change(); 
            
            // Kolom file & url jadi nullable (karena konten utamanya di description)
            $table->string('file_path')->nullable()->change();
            $table->string('file_name')->nullable()->change();
            $table->unsignedBigInteger('file_size')->nullable()->change();
            $table->string('type')->default('mixed')->change(); // Default tipe baru
        });
    }

    public function down()
    {
        // Revert logic (opsional)
    }
};