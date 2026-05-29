<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'title',
        'description',
        'order',
        'total_questions',
        'marks_per_question',
        'negative_marks_per_question',
        'shuffle_questions',
        'time_limit_minutes',
    ];

    protected $casts = [
        'shuffle_questions' => 'boolean',
        'negative_marks_per_question' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class, 'section_id')->orderBy('order');
    }
}
