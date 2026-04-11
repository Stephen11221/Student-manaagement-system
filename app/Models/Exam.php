<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'class_id',
        'trainer_id',
        'title',
        'description',
        'exam_date',
        'exam_mode',
        'submission_type',
        'status',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function class()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function submissions()
    {
        return $this->hasMany(ExamSubmission::class);
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('sort_order');
    }

    public function isOnline(): bool
    {
        return ($this->exam_mode ?? 'online') === 'online';
    }
}
