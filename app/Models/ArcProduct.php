<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ArcProduct extends Model
{
    use AsSource, Filterable;
    protected $fillable = [
        'group_id',
        'name',
        'prot',
        'fat',
        'carb',
        'gi',
    ];
    public function arc_group()
    {
        return $this->belongsTo(ArcGroup::class, 'group_id', 'id');
    }
}
