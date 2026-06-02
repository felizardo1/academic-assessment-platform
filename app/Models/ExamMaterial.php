<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMaterial extends Model
{
    protected $fillable = [
        'exam_id',
        'filename',
        'file_path',
        'file_type',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
