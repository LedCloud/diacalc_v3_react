<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class ArcGroup extends Model
{
    use AsSource, Filterable;
    protected $fillable = ['name'];

    protected $allowedSorts = [
        'name',
    ];
    public function arc_products(): HasMany
    {
        return $this->hasMany(ArcProduct::class, 'group_id', 'id');
    }
}
