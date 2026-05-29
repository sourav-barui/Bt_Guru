<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'answer_text',
        'is_correct',
        'marks_obtained',
        'negative_marks',
        'answered_at',
        'time_spent_seconds',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
        'marks_obtained' => 'decimal:2',
        'negative_marks' => 'decimal:2',
        'is_correct' => 'boolean',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class);
    }

    public function question()
    {
        return $this->belongsTo(ExamQuestion::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(ExamQuestionOption::class, 'selected_option_id');
    }

    // Grade the answer
    public function grade()
    {
        $question = $this->question;
        $correctOptions = $question->correctOptions->pluck('id');
        
        if ($question->question_type === 'single_choice' || $question->question_type === 'true_false') {
            $this->is_correct = $correctOptions->contains($this->selected_option_id);
            
            if ($this->is_correct) {
                $this->marks_obtained = $question->marks;
                $this->negative_marks = 0;
            } else {
                $this->marks_obtained = 0;
                $this->negative_marks = $question->negative_marks;
            }
        }
        // Multiple choice logic can be added here
        
        $this->save();
    }
}
