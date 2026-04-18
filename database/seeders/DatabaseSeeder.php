<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FeePayment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $accounts = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'role' => 'admin',
                'department' => 'Administration',
            ],
            [
                'name' => 'Trainer User',
                'email' => 'trainer@example.com',
                'role' => 'trainer',
                'department' => 'Academics',
            ],
            [
                'name' => 'Career Coach',
                'email' => 'coach@example.com',
                'role' => 'career_coach',
                'department' => 'Student Success',
            ],
            [
                'name' => 'Accountant User',
                'email' => 'accountant@example.com',
                'role' => 'accountant',
                'department' => 'Finance',
            ],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'student',
                'department' => 'General Studies',
            ],
            [
                'name' => 'Partial Student',
                'email' => 'partial.student@example.com',
                'role' => 'student',
                'department' => 'General Studies',
            ],
            [
                'name' => 'Unpaid Student',
                'email' => 'unpaid.student@example.com',
                'role' => 'student',
                'department' => 'General Studies',
            ],
        ];

        foreach ($accounts as $account) {
            User::query()->firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'department' => $account['department'],
                    'password' => 'password',
                ]
            );
        }

        $student = User::where('email', 'test@example.com')->first();
        $coach = User::where('email', 'coach@example.com')->first();

        if ($student && $coach && ! $student->career_coach_id) {
            $student->update(['career_coach_id' => $coach->id]);
        }

        if ($student) {
            FeePayment::query()->firstOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year' => now()->year . '/' . (now()->year + 1),
                    'term' => 'Term 1',
                    'receipt_number' => 'RCPT-2026-0001',
                ],
                [
                    'amount_due' => 50000,
                    'amount_paid' => 50000,
                    'payment_method' => 'cash',
                    'paid_at' => now(),
                    'status' => 'paid',
                    'notes' => 'Sample full payment for accountant dashboard preview.',
                ]
            );
        }

        $partialStudent = User::where('email', 'partial.student@example.com')->first();

        if ($partialStudent) {
            FeePayment::query()->firstOrCreate(
                [
                    'student_id' => $partialStudent->id,
                    'academic_year' => now()->year . '/' . (now()->year + 1),
                    'term' => 'Term 1',
                    'receipt_number' => 'RCPT-2026-0002',
                ],
                [
                    'amount_due' => 50000,
                    'amount_paid' => 20000,
                    'payment_method' => 'bank transfer',
                    'paid_at' => now()->subDays(2),
                    'status' => 'partial',
                    'notes' => 'Sample partial payment for accountant dashboard preview.',
                ]
            );
        }
    }
}
