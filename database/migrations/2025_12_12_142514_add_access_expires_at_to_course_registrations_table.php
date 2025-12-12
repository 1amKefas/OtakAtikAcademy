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
        Schema::table('course_registrations', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('course_registrations', 'access_expires_at')) {
                $table->timestamp('access_expires_at')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('course_registrations', 'expiry_notification_sent')) {
                $table->boolean('expiry_notification_sent')->default(false)->after('access_expires_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropColumn(['access_expires_at', 'expiry_notification_sent']);
        });
    }
};
