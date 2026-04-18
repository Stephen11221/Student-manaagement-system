<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained('accounting_invoices')->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('chart_accounts')->nullOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['cash', 'bank', 'mobile_money', 'card', 'cheque', 'other'])->default('cash');
            $table->string('reference')->nullable()->index();
            $table->string('payer_name')->nullable();
            $table->timestamp('paid_at');
            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->index(['payment_method', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_payments');
    }
};
