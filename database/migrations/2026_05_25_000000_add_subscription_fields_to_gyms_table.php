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
            $table->string('subscription_plan')->nullable()->after('email');
            $table->string('subscription_status')->default('inactive')->after('subscription_plan');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_status');
            $table->string('subscription_transaction_id')->nullable()->after('subscription_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_plan',
                'subscription_status',
                'subscription_expires_at',
                'subscription_transaction_id'
            ]);
        });
    }
};
