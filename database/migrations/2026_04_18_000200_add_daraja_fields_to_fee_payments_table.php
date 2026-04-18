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
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('payment_method');
            $table->string('checkout_request_id')->nullable()->index()->after('notes');
            $table->string('merchant_request_id')->nullable()->index()->after('checkout_request_id');
            $table->string('transaction_id')->nullable()->index()->after('merchant_request_id');
            $table->string('daraja_response_code')->nullable()->after('transaction_id');
            $table->text('daraja_response_description')->nullable()->after('daraja_response_code');
            $table->json('daraja_payload')->nullable()->after('daraja_response_description');
            $table->timestamp('daraja_requested_at')->nullable()->after('daraja_payload');
            $table->timestamp('daraja_completed_at')->nullable()->after('daraja_requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_payments', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'checkout_request_id',
                'merchant_request_id',
                'transaction_id',
                'daraja_response_code',
                'daraja_response_description',
                'daraja_payload',
                'daraja_requested_at',
                'daraja_completed_at',
            ]);
        });
    }
};
