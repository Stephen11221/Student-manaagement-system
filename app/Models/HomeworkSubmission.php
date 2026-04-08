<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeworkSubmission extends Model
{
    protected $fillable = [
        'homework_id',
        'student_id',
        'content',
        'file_path',
        'marks',
        'feedback',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function homework()
    {
        return $this->belongsTo(Homework::class, 'homework_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
