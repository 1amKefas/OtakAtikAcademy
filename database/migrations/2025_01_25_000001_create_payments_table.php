<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('order_id')->unique();
            $table->decimal('gross_amount', 15, 2);
            $table->string('payment_type')->nullable();
            $table->string('transaction_status');
            $table->string('transaction_id')->nullable();
            $table->timestamp('transaction_time')->nullable();
            $table->timestamp('settlement_time')->nullable();
            $table->string('status_code')->nullable();
            $table->text('status_message')->nullable();
            $table->json('payment_data')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'course_id']);
            $table->index('order_id');
            $table->index('transaction_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};