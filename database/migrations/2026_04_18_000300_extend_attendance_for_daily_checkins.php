<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->date('attendance_date')->nullable()->after('timetable_id');
            $table->string('scope_type')->nullable()->after('attendance_date');
            $table->unsignedBigInteger('scope_id')->nullable()->after('scope_type');
            $table->foreignId('department_id')->nullable()->after('class_id')->constrained()->nullOnDelete();
            $table->time('check_in_at')->nullable()->after('status');
            $table->time('check_out_at')->nullable()->after('check_in_at');
            $table->foreignId('recorded_by')->nullable()->after('marked_at')->constrained('users')->nullOnDelete();
            $table->string('source')->default('manual')->after('recorded_by');
        });

        DB::table('attendance')
            ->whereNull('attendance_date')
            ->update([
                'attendance_date' => DB::raw('DATE(marked_at)'),
                'scope_type' => DB::raw("CASE WHEN class_id IS NOT NULL THEN 'class' ELSE 'global' END"),
                'scope_id' => DB::raw('COALESCE(class_id, 0)'),
            ]);

        Schema::table('attendance', function (Blueprint $table) {
            $table->index('attendance_date');
            $table->index(['scope_type', 'scope_id']);
            $table->index('student_id');
            $table->index(['attendance_date', 'status']);
            $table->unique(['attendance_date', 'scope_type', 'scope_id', 'student_id'], 'attendance_unique_daily_scope');
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropUnique('attendance_unique_daily_scope');
            $table->dropIndex(['attendance_date']);
            $table->dropIndex(['scope_type', 'scope_id']);
            $table->dropIndex(['student_id']);
            $table->dropIndex(['attendance_date', 'status']);
            $table->dropColumn([
                'attendance_date',
                'scope_type',
                'scope_id',
                'department_id',
                'check_in_at',
                'check_out_at',
                'recorded_by',
                'source',
            ]);
        });
    }
};
