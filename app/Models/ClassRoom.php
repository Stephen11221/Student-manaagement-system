<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = [
        'name',
        'room_number',
        'delivery_mode',
        'description',
        'trainer_id',
        'status',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'class_id');
    }

    public function homeworks()
    {
        return $this->hasMany(Homework::class, 'class_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'class_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'student_id');
    }

    public function isOnline(): bool
    {
        return $this->delivery_mode === 'online';
    }

    public function isPhysical(): bool
    {
        return $this->delivery_mode !== 'online';
    }
}
