<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiaryMeal extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'diary_id',
        'gl1','gl2',
        'k1', 'k2', 'k3',
    ];

    public function diary(): BelongsTo
    {
        return $this->belongsTo(Diary::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(DiaryMealProduct::class);
    }

    public function toDiaryArray(): array
    {
        $weight = 0.0;
        $prot = 0.0;
        $fat = 0.0;
        $carb = 0.0;
        $qcarb = 0.0;
        $calories = 0.0;
        $products = [];

        foreach ($this->products as $product) {
            $products[] = $product->toDiaryArray();
            $weight += (float) $product->weight;
            $prot += $product->gramsProt();
            $fat += $product->gramsFat();
            $carb += $product->gramsCarb();
            $qcarb += $product->glycemicLoad();
            $calories += $product->calories();
        }

        return [
            'id' => $this->id,
            'gl1' => (float) $this->gl1,
            'gl2' => (float) $this->gl2,
            'k1' => (float) $this->k1,
            'k2' => (float) $this->k2,
            'k3' => (float) $this->k3,
            'weight' => $weight,
            'prot' => $weight > 0 ? 100 * $prot / $weight : 0.0,
            'fat' => $weight > 0 ? 100 * $fat / $weight : 0.0,
            'carb' => $weight > 0 ? 100 * $carb / $weight : 0.0,
            'gi' => $carb > 0 ? (int) round(100 * $qcarb / $carb) : 50,
            'calories' => $calories,
            'products' => $products,
        ];
    }
}
