<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'attendance_date',
        'scope_type',
        'scope_id',
        'class_id',
        'department_id',
        'student_id',
        'timetable_id',
        'status',
        'check_in_at',
        'check_out_at',
        'remarks',
        'marked_at',
        'recorded_by',
        'source',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'marked_at' => 'datetime',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function getScopeLabelAttribute(): string
    {
        if ($this->scope_type === 'class') {
            return $this->classRoom?->name ?? 'Class';
        }

        if ($this->scope_type === 'department') {
            return $this->department?->name ?? 'Department';
        }

        return 'Attendance';
    }
}
