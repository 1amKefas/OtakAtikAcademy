<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'duration_days')) {
                $table->integer('duration_days')->default(30);
            }
            if (!Schema::hasColumn('courses', 'start_date')) {
                $table->date('start_date')->nullable();
            }
            if (!Schema::hasColumn('courses', 'end_date')) {
                $table->date('end_date')->nullable();
            }
            if (!Schema::hasColumn('courses', 'is_rescheduled')) {
                $table->boolean('is_rescheduled')->default(false);
            }
            if (!Schema::hasColumn('courses', 'rescheduled_start_date')) {
                $table->date('rescheduled_start_date')->nullable();
            }
            if (!Schema::hasColumn('courses', 'reschedule_reason')) {
                $table->string('reschedule_reason')->nullable();
            }
            if (!Schema::hasColumn('courses', 'quota_not_met')) {
                $table->boolean('quota_not_met')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'duration_days')) {
                $table->dropColumn('duration_days');
            }
            if (Schema::hasColumn('courses', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('courses', 'end_date')) {
                $table->dropColumn('end_date');
            }
            if (Schema::hasColumn('courses', 'is_rescheduled')) {
                $table->dropColumn('is_rescheduled');
            }
            if (Schema::hasColumn('courses', 'rescheduled_start_date')) {
                $table->dropColumn('rescheduled_start_date');
            }
            if (Schema::hasColumn('courses', 'reschedule_reason')) {
                $table->dropColumn('reschedule_reason');
            }
            if (Schema::hasColumn('courses', 'quota_not_met')) {
                $table->dropColumn('quota_not_met');
            }
        });
    }
};