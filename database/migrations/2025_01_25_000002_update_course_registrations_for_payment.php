<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->string('order_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('payment_notes')->nullable();
            $table->decimal('final_price', 10, 2)->nullable()->change();
            $table->index('order_id');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('course_registrations', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['paid_at']);
            $table->dropColumn(['order_id', 'payment_method', 'paid_at', 'payment_notes']);
        });
    }
};