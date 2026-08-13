<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiaryMealProduct extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'k1','k2','k3',
        'prot','fat','carb', 'gi', 'weight',
        'diary_meal_id',
    ];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(DiaryMeal::class);
    }
}
