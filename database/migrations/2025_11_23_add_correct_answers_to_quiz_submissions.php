<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_submissions', function (Blueprint $table) {
            $table->integer('correct_answers')->default(0)->after('score');
            $table->timestamp('graded_at')->nullable()->after('submitted_at');
            $table->string('graded_by')->nullable()->after('graded_at');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_submissions', function (Blueprint $table) {
            $table->dropColumn(['correct_answers', 'graded_at', 'graded_by']);
        });
    }
};
