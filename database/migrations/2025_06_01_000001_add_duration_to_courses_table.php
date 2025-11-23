<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurationToCoursesTable extends Migration
{
    public function up()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'start_date')) {
                $table->date('start_date')->nullable()->after('duration_days');
            }
            if (!Schema::hasColumn('courses', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('courses', 'is_rescheduled')) {
                $table->boolean('is_rescheduled')->default(false)->after('end_date');
            }
            if (!Schema::hasColumn('courses', 'reschedule_reason')) {
                $table->string('reschedule_reason')->nullable()->after('is_rescheduled');
            }
            if (!Schema::hasColumn('courses', 'quota_not_met')) {
                $table->boolean('quota_not_met')->default(false)->after('reschedule_reason');
            }
        });
    }

    public function down()
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('courses', 'end_date')) {
                $table->dropColumn('end_date');
            }
            if (Schema::hasColumn('courses', 'is_rescheduled')) {
                $table->dropColumn('is_rescheduled');
            }
            if (Schema::hasColumn('courses', 'reschedule_reason')) {
                $table->dropColumn('reschedule_reason');
            }
            if (Schema::hasColumn('courses', 'quota_not_met')) {
                $table->dropColumn('quota_not_met');
            }
        });
    }
}