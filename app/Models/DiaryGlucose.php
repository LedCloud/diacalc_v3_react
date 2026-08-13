<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryGlucose extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'gl',
        'diary_id',
    ];

    public function diary(): BelongsTo
    {
        return $this->belongsTo(Diary::class);
    }
}
