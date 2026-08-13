<?php

namespace App\Models;

use App\Classes\Diacalc\Glucose;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factor extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'time',
        'k1','k2','k3',
    ];

    /*protected $casts = [
        'time' => 'datetime',
    ];*/

    protected function time(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => date('H:i', strtotime($value)),
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getK3FormattedAttribute()
    {
        $settings = $this->user()->first()->getSetting('User');
        return (new Glucose($this->k3))
            ->setMmol($settings['is_mmol'])
            ->setPlasma($settings['is_plasma'])
            ->getForView();
    }

    public function getK2FormattedAttribute()
    {
        return number_format($this->k2, 2);
    }
    public function getK1FormattedAttribute()
    {
        return number_format($this->k1, 2);
    }
}
