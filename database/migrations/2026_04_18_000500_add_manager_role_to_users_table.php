<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'trainer', 'admin', 'career_coach', 'department_admin', 'accountant', 'manager') NOT NULL DEFAULT 'student' AFTER email");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('student', 'trainer', 'admin', 'career_coach', 'department_admin', 'accountant') NOT NULL DEFAULT 'student' AFTER email");
        }
    }
};
