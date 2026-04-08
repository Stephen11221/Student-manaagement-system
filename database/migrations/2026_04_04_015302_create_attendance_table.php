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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('timetable_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'late'])->default('absent');
            $table->text('remarks')->nullable();
            $table->timestamp('marked_at')->nullable();
            $table->timestamps();
            $table->unique(['class_id', 'student_id', 'timetable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
