<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiaryMealProduct extends Model
{
    public const KCAL_PROT = 4.1;
    public const KCAL_FAT = 9.3;
    public const KCAL_CARB = 4.1;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'k1','k2','k3',
        'prot','fat','carb', 'gi', 'weight',
        'diary_meal_id',
    ];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(DiaryMeal::class);
    }

    public function gramsProt(): float
    {
        return ((float) $this->prot * (float) $this->weight) / 100;
    }

    public function gramsFat(): float
    {
        return ((float) $this->fat * (float) $this->weight) / 100;
    }

    public function gramsCarb(): float
    {
        return ((float) $this->carb * (float) $this->weight) / 100;
    }

    public function glycemicLoad(): float
    {
        return ((float) $this->carb * (float) $this->weight * (int) $this->gi) / 10000;
    }

    public function calories(): float
    {
        return $this->gramsProt() * self::KCAL_PROT
            + $this->gramsFat() * self::KCAL_FAT
            + $this->gramsCarb() * self::KCAL_CARB;
    }

    public function toDiaryArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'prot' => (float) $this->prot,
            'fat' => (float) $this->fat,
            'carb' => (float) $this->carb,
            'gi' => (int) $this->gi,
            'weight' => (float) $this->weight,
            'calories' => $this->calories(),
        ];
    }
}
