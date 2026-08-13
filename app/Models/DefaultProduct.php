<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultProduct extends Model
{
    protected $fillable = [
        'group_id',
        'name',
        'prot',
        'fat',
        'carb',
        'gi',
    ];

    public function default_group()
    {
        return $this->belongsTo(DefaultGroup::class, 'group_id', 'id');
    }
}
