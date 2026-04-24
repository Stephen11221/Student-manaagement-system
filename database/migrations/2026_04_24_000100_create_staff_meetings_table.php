<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('team_role')->index();
            $table->enum('audience_type', ['team', 'individual'])->default('team');
            $table->foreignId('staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('meeting_type', ['online', 'physical'])->default('online');
            $table->string('title');
            $table->timestamp('scheduled_at');
            $table->string('location')->nullable();
            $table->string('meeting_link')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['team_role', 'audience_type']);
            $table->index(['team_role', 'meeting_type']);
            $table->index('scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_meetings');
    }
};
