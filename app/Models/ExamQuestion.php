<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    protected $fillable = [
        'exam_id',
        'question_text',
        'sort_order',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
