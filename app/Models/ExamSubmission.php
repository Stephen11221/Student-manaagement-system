<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSubmission extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'content',
        'answers_json',
        'file_path',
        'marks',
        'feedback',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'answers_json' => 'array',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
