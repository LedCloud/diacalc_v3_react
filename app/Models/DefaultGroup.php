<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DefaultGroup extends Model
{
    protected $fillable = ['name'];

    public function default_products(): HasMany
    {
        return $this->hasMany(DefaultProduct::class, 'group_id', 'id');
    }
}
