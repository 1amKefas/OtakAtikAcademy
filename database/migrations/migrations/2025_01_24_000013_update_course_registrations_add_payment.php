<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('discount_code'); // bank_transfer, credit_card, ewallet
            $table->string('payment_proof')->nullable()->after('payment_method'); // Bukti transfer
            $table->timestamp('payment_date')->nullable()->after('payment_proof');
            $table->string('voucher_code')->nullable()->after('discount_code');
            $table->decimal('voucher_discount', 10, 2)->default(0)->after('voucher_code');
        });
    }

    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_proof', 'payment_date', 'voucher_code', 'voucher_discount']);
        });
    }
};