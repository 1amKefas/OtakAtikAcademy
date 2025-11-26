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
            // Drop unique constraint on email
            $table->dropUnique(['email']);
            // Add composite unique on email + google_id (allows duplicate emails with different google_ids)
            $table->unique(['email', 'google_id'], 'users_email_google_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert back
            $table->dropUnique('users_email_google_unique');
            $table->unique(['email']);
        });
    }
};
