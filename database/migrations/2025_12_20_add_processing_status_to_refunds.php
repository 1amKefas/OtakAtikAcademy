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
        // This migration adds support for 'processing' status
        // The refunds table likely already has a status column
        // This just documents that status can be: pending, processing, completed, rejected
        
        // No schema changes needed - just updating column comment if needed
        Schema::table('refunds', function (Blueprint $table) {
            // Assuming status column exists with default 'pending'
            // We just add processing_started_at and completed_at timestamps
            if (!Schema::hasColumn('refunds', 'processing_started_at')) {
                $table->timestamp('processing_started_at')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('refunds', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('processing_started_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn(['processing_started_at', 'completed_at']);
        });
    }
};
