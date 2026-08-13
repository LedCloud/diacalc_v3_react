<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'prot','fat','carb', 'gi', 'weight','used',
        'product_group_id',
        'product_id',
    ];

    /**
     * Get the user that owns eating.
     */
    public function productGroup(): BelongsTo
    {
        return $this->belongsTo(ProductGroup::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Content of the complex product
     * @return HasMany
     */
    public function content()
    {
        return $this->hasMany(Product::class, 'product_id');
    }
}
