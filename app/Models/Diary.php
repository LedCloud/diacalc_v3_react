<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Diary extends Model
{
    // Override Laravel's plural naming convention
    protected $table = 'diary';

    public $timestamps = false;

    protected $fillable = [
        'timestamp',
        'type',
        'comment',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function glucose(): HasMany
    {
        return $this->hasMany(DiaryGlucose::class);
    }

    public function meals(): HasMany
    {
        return $this->hasMany(DiaryMeal::class);
    }
}
