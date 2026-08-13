<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Orchid\Filters\Types\Like;
use Orchid\Filters\Types\Where;
use Orchid\Filters\Types\WhereDateStartEnd;
use Orchid\Platform\Models\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'permissions',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'permissions'          => 'array',
        'email_verified_at'    => 'datetime',
    ];

    /**
     * The attributes for which you can use filters in url.
     *
     * @var array
     */
    protected $allowedFilters = [
           'id'         => Where::class,
           'name'       => Like::class,
           'email'      => Like::class,
           'updated_at' => WhereDateStartEnd::class,
           'created_at' => WhereDateStartEnd::class,
    ];

    /**
     * The attributes for which can use sort in url.
     *
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'email',
        'updated_at',
        'created_at',
    ];

    /**
     * Get eating associated with the user.
     */
    public function eating(): HasOne
    {
        return $this->hasOne(Eating::class);
    }
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    public function getSetting($key)
    {
        $default = [];
        $path = "\\App\\Classes\\Settings\\" . $key . "Setting";
        if (class_exists($path)) {
            $default = $path::DEFAULT;
        }

        // Ищем запись в связанных настройках по полю 'key'
        $setting = $this->settings()->where('key', $key)->first();
        if (!empty($setting)) {
            return json_decode($setting->values, true);
        }

        return $default;
    }

    public function putSetting($key, $value = [])
    {
        $default = [];
        $path = "\\App\\Classes\\Settings\\" . $key . "Setting";
        if (class_exists($path)) {
            $default = $path::DEFAULT;
        }
        $merged = json_encode(array_merge($default, $value));

        $this->settings()->upsert([
            'key' => $key,
            'values' => $merged,
            ],['id', 'key'],
            ['values']);
    }

    public function diary(): HasMany
    {
        return $this->hasMany(Diary::class);
    }

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function productGroups(): HasMany
    {
        return $this->hasMany(ProductGroup::class)->orderBy('sort_order', 'asc');
    }

    public function factors(): HasMany
    {
        return $this->hasMany(Factor::class)->orderBy('time', 'asc');
    }
}
