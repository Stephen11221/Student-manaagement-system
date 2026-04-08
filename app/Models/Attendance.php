<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'class_id',
        'student_id',
        'timetable_id',
        'status',
        'remarks',
        'marked_at',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function timetable()
    {
        return $this->belongsTo(Timetable::class);
    }
}
