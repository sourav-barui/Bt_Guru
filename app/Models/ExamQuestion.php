<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'section_id',
        'question_text',
        'question_image',
        'question_type',
        'explanation',
        'marks',
        'negative_marks',
        'order',
    ];

    protected $casts = [
        'marks' => 'integer',
        'negative_marks' => 'decimal:2',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function section()
    {
        return $this->belongsTo(ExamSection::class);
    }

    public function options()
    {
        return $this->hasMany(ExamQuestionOption::class, 'question_id')->orderBy('order');
    }

    public function correctOptions()
    {
        return $this->hasMany(ExamQuestionOption::class, 'question_id')->where('is_correct', true);
    }

    // Get options shuffled or ordered
    public function getOptionsForAttempt($shuffle = false)
    {
        $options = $this->options;
        if ($shuffle) {
            $options = $options->shuffle();
        }
        return $options;
    }
}
