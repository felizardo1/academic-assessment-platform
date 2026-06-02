<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Section;

class Exam extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'institution',
        'subject',
        'date',
        'duration',
        'total_points',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(ExamMaterial::class);
    }
}
