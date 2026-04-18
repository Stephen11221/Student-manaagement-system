<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_journals', function (Blueprint $table) {
            $table->id();
            $table->string('journal_number', 40)->unique();
            $table->date('entry_date');
            $table->string('reference')->nullable()->index();
            $table->string('source_type')->nullable()->index();
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('posted');
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->index(['entry_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_journals');
    }
};
