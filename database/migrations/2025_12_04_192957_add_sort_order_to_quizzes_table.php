<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('quizzes', function (Blueprint $table) {
        $table->integer('sort_order')->default(0)->after('passing_score');
    });
}

public function down()
{
    Schema::table('quizzes', function (Blueprint $table) {
        $table->dropColumn('sort_order');
    });
}
};
