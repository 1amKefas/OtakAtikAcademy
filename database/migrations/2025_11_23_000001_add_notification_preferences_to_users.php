<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Notification Preferences
            $table->boolean('notify_assignment_posted')->default(true)->after('is_instructor');
            $table->boolean('notify_deadline_reminder')->default(true)->after('notify_assignment_posted');
            $table->boolean('notify_quiz_posted')->default(true)->after('notify_deadline_reminder');
            $table->boolean('notify_material_posted')->default(true)->after('notify_quiz_posted');
            $table->boolean('notify_course_enrollment')->default(true)->after('notify_material_posted');
            $table->boolean('notify_forum_reply')->default(true)->after('notify_course_enrollment');
            
            // Reminder Settings
            $table->string('deadline_reminder_days')->default('1,3')->after('notify_forum_reply'); // "1,3" means 1 day and 3 days
            
            // Display/Theme Settings
            $table->string('theme')->default('light')->after('deadline_reminder_days'); // light or dark
            $table->string('language')->default('id')->after('theme'); // id or en
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notify_assignment_posted',
                'notify_deadline_reminder',
                'notify_quiz_posted',
                'notify_material_posted',
                'notify_course_enrollment',
                'notify_forum_reply',
                'deadline_reminder_days',
                'theme',
                'language',
            ]);
        });
    }
};
