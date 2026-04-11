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
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('status')->default('active')->after('is_active'); // active, inactive, suspended, locked
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->unsignedBigInteger('department_id')->nullable()->after('status');
            $table->timestamp('suspended_until')->nullable()->after('department_id');
            $table->text('suspension_reason')->nullable()->after('suspended_until');
        });
        
        // Add foreign key separately to ensure departments table exists first
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['is_active', 'status', 'last_login_at', 'department_id', 'suspended_until', 'suspension_reason']);
        });
    }
};
