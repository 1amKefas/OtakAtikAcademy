<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Profile photo
            $table->string('profile_picture')->nullable()->after('bio');
            
            // Education institution name (sekolah/universitas)
            $table->string('education_name')->nullable()->after('education_level');
            
            // Birth date untuk hitung age (sudah ada sebagai date_of_birth)
            // Age akan dihitung dari date_of_birth
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_picture', 'education_name']);
        });
    }
};
