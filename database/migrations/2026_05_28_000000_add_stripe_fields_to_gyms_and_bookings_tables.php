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
        Schema::table('gyms', function (Blueprint $table) {
            $table->string('stripe_session_id')->nullable()->after('subscription_transaction_id');
            $table->string('payment_method')->nullable()->after('stripe_session_id');
            $table->string('transaction_reference')->nullable()->after('payment_method');
            $table->decimal('amount_paid', 8, 2)->nullable()->after('transaction_reference');
            $table->timestamp('payment_at')->nullable()->after('amount_paid');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->string('stripe_session_id')->nullable()->after('payment_transaction_id');
            $table->string('payment_method')->nullable()->after('stripe_session_id');
            $table->string('transaction_reference')->nullable()->after('payment_method');
            $table->decimal('amount_paid', 8, 2)->nullable()->after('transaction_reference');
            $table->timestamp('payment_at')->nullable()->after('amount_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_session_id',
                'payment_method',
                'transaction_reference',
                'amount_paid',
                'payment_at'
            ]);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_session_id',
                'payment_method',
                'transaction_reference',
                'amount_paid',
                'payment_at'
            ]);
        });
    }
};
