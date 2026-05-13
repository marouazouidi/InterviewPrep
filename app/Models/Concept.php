<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Concept extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'explanation', 'difficulty', 'status', 'domain_id'];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function generatedQuestions(): HasMany
    {
        return $this->hasMany(GeneratedQuestion::class);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'to_review'   => 'To Review',
                'in_progress' => 'In Progress',
                'mastered'    => 'Mastered',
                default       => $this->status,
            }
        );
    }

    protected function difficultyLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->difficulty) {
                'junior' => 'Junior',
                'mid'    => 'Mid',
                'senior' => 'Senior',
                default  => $this->difficulty,
            }
        );
    }
}
