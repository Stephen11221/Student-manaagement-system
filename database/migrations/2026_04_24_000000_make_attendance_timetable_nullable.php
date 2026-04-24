<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `attendance` DROP FOREIGN KEY `attendance_timetable_id_foreign`');
        DB::statement('ALTER TABLE `attendance` MODIFY `timetable_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `attendance` ADD CONSTRAINT `attendance_timetable_id_foreign` FOREIGN KEY (`timetable_id`) REFERENCES `timetables` (`id`) ON DELETE SET NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `attendance` DROP FOREIGN KEY `attendance_timetable_id_foreign`');
        DB::statement('ALTER TABLE `attendance` MODIFY `timetable_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `attendance` ADD CONSTRAINT `attendance_timetable_id_foreign` FOREIGN KEY (`timetable_id`) REFERENCES `timetables` (`id`) ON DELETE CASCADE');
    }
};
