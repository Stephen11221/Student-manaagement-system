<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'career_coach_id',
        'admission_number',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'guardian_name',
        'guardian_phone',
        'guardian_relationship',
        'current_class_id',
        'stream',
        'student_status',
        'admission_date',
        'exit_date',
        'transfer_notes',
        'birth_certificate_path',
        'report_form_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'admission_date' => 'date',
            'exit_date' => 'date',
        ];
    }

    /**
     * Get classes taught by this trainer
     */
    public function taughtClasses()
    {
        return $this->hasMany(ClassRoom::class, 'trainer_id');
    }

    /**
     * Get classes enrolled in by this student
     */
    public function enrolledClasses()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_student', 'student_id', 'class_id')
            ->select('class_rooms.*');
    }

    /**
     * Get homework for this user (as trainer)
     */
    public function homeworks()
    {
        return $this->hasMany(Homework::class, 'trainer_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'trainer_id');
    }

    /**
     * Get homework submissions from this student
     */
    public function homeworkSubmissions()
    {
        return $this->hasMany(HomeworkSubmission::class, 'student_id');
    }

    public function examSubmissions()
    {
        return $this->hasMany(ExamSubmission::class, 'student_id');
    }

    /**
     * Get notifications for this user
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Career coach assigned to this student.
     */
    public function careerCoach()
    {
        return $this->belongsTo(User::class, 'career_coach_id');
    }

    /**
     * Students assigned to this career coach.
     */
    public function assignedStudents()
    {
        return $this->hasMany(User::class, 'career_coach_id');
    }

    /**
     * Student's primary class assignment.
     */
    public function currentClass()
    {
        return $this->belongsTo(ClassRoom::class, 'current_class_id');
    }

    /**
     * Get attendance records for this student
     */
    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is a trainer
     */
    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is a department admin
     */
    public function isDepartmentAdmin(): bool
    {
        return $this->role === 'department_admin';
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user is a career coach.
     */
    public function isCareerCoach(): bool
    {
        return $this->role === 'career_coach';
    }
}
