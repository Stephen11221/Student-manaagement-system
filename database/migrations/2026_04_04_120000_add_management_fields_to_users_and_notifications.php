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
            if (! Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('role');
            }

            if (! Schema::hasColumn('users', 'career_coach_id')) {
                $table->foreignId('career_coach_id')
                    ->nullable()
                    ->after('department')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('read');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'read_at')) {
                $table->dropColumn('read_at');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'career_coach_id')) {
                $table->dropConstrainedForeignId('career_coach_id');
            }

            if (Schema::hasColumn('users', 'department')) {
                $table->dropColumn('department');
            }

            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
