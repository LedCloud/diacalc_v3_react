<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiaryMeal extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'diary_id',
        'gl1','gl2',
        'k1', 'k2', 'k3',
    ];

    public function diary(): BelongsTo
    {
        return $this->belongsTo(Diary::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(DiaryMealProduct::class);
    }
}
