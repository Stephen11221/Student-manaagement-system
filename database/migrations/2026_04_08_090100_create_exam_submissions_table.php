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
        Schema::create('exam_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->longText('content')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedInteger('marks')->nullable();
            $table->text('feedback')->nullable();
            $table->enum('status', ['draft', 'submitted', 'graded'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['exam_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_submissions');
    }
};
