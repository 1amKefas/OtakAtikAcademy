<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('quiz_submissions', function (Blueprint $table) {
            $table->string('status')->default('in_progress')->after('submitted_at');
            // Tambahin sekalian kolom jumlah benar biar gak error nanti
            $table->integer('correct_answers_count')->default(0)->after('score'); 
        });
    }

    public function down()
    {
        Schema::table('quiz_submissions', function (Blueprint $table) {
            $table->dropColumn(['status', 'correct_answers_count']);
        });
    }
};
