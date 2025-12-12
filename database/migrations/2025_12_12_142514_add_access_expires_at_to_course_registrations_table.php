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
            // Menyimpan tanggal kadaluarsa akses user
            $table->timestamp('access_expires_at')->nullable()->after('status');
            // Menyimpan status apakah user sudah diingatkan (untuk cron job)
            $table->boolean('expiry_notification_sent')->default(false)->after('access_expires_at');
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
