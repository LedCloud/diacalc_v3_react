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
            $decoded = json_decode($setting->values, true);
            $decoded = $this->flattenSettingValues($decoded);
            return array_merge($default, $decoded);
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
        $value = $this->flattenSettingValues($value);
        $mergedArr = array_merge($default, $value);
        $merged = json_encode($mergedArr);

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

    /**
     * Copy stored a list of setting maps under numeric keys (e.g. "0").
     * Promote that nested map and drop the alias calorie_limit.
     */
    protected function flattenSettingValues($value): array
    {
        if (!is_array($value)) {
            return [];
        }

        if (isset($value[0]) && is_array($value[0])) {
            $nested = $value[0];
            unset($value[0]);
            $value = array_merge($value, $nested);
        }

        if (array_key_exists('calorie_limit', $value)) {
            if (!array_key_exists('calory_limit', $value) || $value['calory_limit'] === null) {
                $value['calory_limit'] = $value['calorie_limit'];
            }
            unset($value['calorie_limit']);
        }

        return $value;
    }
}
