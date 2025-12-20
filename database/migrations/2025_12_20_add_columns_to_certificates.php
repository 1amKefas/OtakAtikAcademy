<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('certificates', 'completion_date')) {
                $table->date('completion_date')->nullable()->after('certificate_number');
            }
            
            if (!Schema::hasColumn('certificates', 'instructor_name')) {
                $table->string('instructor_name')->nullable()->after('completion_date');
            }
            
            if (!Schema::hasColumn('certificates', 'instructor_title')) {
                $table->string('instructor_title')->nullable()->after('instructor_name');
            }
            
            if (!Schema::hasColumn('certificates', 'instructor_company')) {
                $table->string('instructor_company')->nullable()->after('instructor_title');
            }
            
            if (!Schema::hasColumn('certificates', 'instructor_signature_path')) {
                $table->string('instructor_signature_path')->nullable()->after('instructor_company');
            }
            
            if (!Schema::hasColumn('certificates', 'verification_code')) {
                $table->string('verification_code')->unique()->nullable()->after('instructor_signature_path');
            }
            
            if (!Schema::hasColumn('certificates', 'is_downloaded')) {
                $table->boolean('is_downloaded')->default(false)->after('verification_code');
            }
            
            if (!Schema::hasColumn('certificates', 'downloaded_at')) {
                $table->datetime('downloaded_at')->nullable()->after('is_downloaded');
            }
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $columns = [
                'completion_date',
                'instructor_name',
                'instructor_title',
                'instructor_company',
                'instructor_signature_path',
                'verification_code',
                'is_downloaded',
                'downloaded_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('certificates', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
