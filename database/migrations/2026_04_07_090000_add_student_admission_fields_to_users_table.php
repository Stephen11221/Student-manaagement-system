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
        Schema::table('users', function (Blueprint $table) {
            $table->string('admission_number')->nullable()->unique()->after('email');
            $table->date('date_of_birth')->nullable()->after('career_coach_id');
            $table->string('gender')->nullable()->after('date_of_birth');
            $table->string('phone')->nullable()->after('gender');
            $table->text('address')->nullable()->after('phone');
            $table->string('guardian_name')->nullable()->after('address');
            $table->string('guardian_phone')->nullable()->after('guardian_name');
            $table->string('guardian_relationship')->nullable()->after('guardian_phone');
            $table->foreignId('current_class_id')->nullable()->after('guardian_relationship')->constrained('class_rooms')->nullOnDelete();
            $table->string('stream')->nullable()->after('current_class_id');
            $table->string('student_status')->nullable()->after('stream');
            $table->date('admission_date')->nullable()->after('student_status');
            $table->date('exit_date')->nullable()->after('admission_date');
            $table->text('transfer_notes')->nullable()->after('exit_date');
            $table->string('birth_certificate_path')->nullable()->after('transfer_notes');
            $table->string('report_form_path')->nullable()->after('birth_certificate_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('current_class_id');
            $table->dropColumn([
                'admission_number',
                'date_of_birth',
                'gender',
                'phone',
                'address',
                'guardian_name',
                'guardian_phone',
                'guardian_relationship',
                'stream',
                'student_status',
                'admission_date',
                'exit_date',
                'transfer_notes',
                'birth_certificate_path',
                'report_form_path',
            ]);
        });
    }
};
