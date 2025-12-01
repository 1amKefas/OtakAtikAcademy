<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            // Ubah kolom jadi nullable agar Essay bisa masuk tanpa error
            $table->json('options')->nullable()->change();
            $table->string('correct_answer')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            // Kembalikan ke wajib isi (hati-hati jika ada data essay)
            // $table->json('options')->nullable(false)->change();
            // $table->string('correct_answer')->nullable(false)->change();
        });
    }
};