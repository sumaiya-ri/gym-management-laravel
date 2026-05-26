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
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('gym_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('payment_status')->default('pending')->after('booking_date');
            $table->decimal('payment_amount', 8, 2)->default(0.00)->after('payment_status');
            $table->string('payment_transaction_id')->nullable()->after('payment_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['gym_id']);
            $table->dropColumn(['gym_id', 'payment_status', 'payment_amount', 'payment_transaction_id']);
        });
    }
};
