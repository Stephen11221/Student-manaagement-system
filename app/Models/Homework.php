<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    protected $table = 'homework';

    protected $fillable = [
        'class_id',
        'trainer_id',
        'title',
        'description',
        'due_date',
        'submission_type',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
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
        return $this->hasMany(HomeworkSubmission::class, 'homework_id');
    }
}
