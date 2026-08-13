<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Eating extends Model
{
    protected $fillable = [
        'k1','k2','k3','gl1','gl2','be','eaten', 'eaten_date'
    ];

    /**
     * Get the user that owns eating.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
