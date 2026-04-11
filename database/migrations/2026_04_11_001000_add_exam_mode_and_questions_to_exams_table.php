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
        Schema::table('exams', function (Blueprint $table) {
            if (! Schema::hasColumn('exams', 'exam_mode')) {
                $table->enum('exam_mode', ['online', 'physical'])->default('online')->after('status');
            }
        });

        Schema::table('exam_submissions', function (Blueprint $table) {
            if (! Schema::hasColumn('exam_submissions', 'answers_json')) {
                $table->json('answers_json')->nullable()->after('content');
            }
        });

        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->text('question_text');
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();

            $table->unique(['exam_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');

        Schema::table('exam_submissions', function (Blueprint $table) {
            if (Schema::hasColumn('exam_submissions', 'answers_json')) {
                $table->dropColumn('answers_json');
            }
        });

        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'exam_mode')) {
                $table->dropColumn('exam_mode');
            }
        });
    }
};
