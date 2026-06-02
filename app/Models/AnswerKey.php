<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnswerKey extends Model
{
       protected $fillable = [
        'question_id',
        'expected_answer',
        'correction_criteria',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
