<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'prot','fat','carb', 'gi', 'weight',
        'is_snack', 'user_id', 'product_id',
    ];

    /**
     * Get the user that owns eating.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
