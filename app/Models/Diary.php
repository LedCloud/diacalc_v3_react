<?php

namespace App\Models;

use App\Classes\Enum\DiaryRecordType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Diary extends Model
{
    // Override Laravel's plural naming convention
    protected $table = 'diary';

    public $timestamps = false;

    protected $fillable = [
        'timestamp',
        'type',
        'comment',
        'user_id',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function glucose(): HasMany
    {
        return $this->hasMany(DiaryGlucose::class);
    }

    public function meals(): HasMany
    {
        return $this->hasMany(DiaryMeal::class);
    }

    public static function daysForUserRange(int $userId, Carbon $start, Carbon $end): array
    {
        $records = static::query()
            ->where('user_id', $userId)
            ->with(['glucose', 'meals.products'])
            ->where('timestamp', '>=', $start->copy()->startOfDay())
            ->where('timestamp', '<', $end->copy()->startOfDay()->addDay())
            ->orderBy('timestamp')
            ->get();

        return static::groupByDays($records);
    }

    public static function groupByDays(Collection $records): array
    {
        $days = [];

        foreach ($records as $record) {
            $date = $record->timestamp->toDateString();
            if (!isset($days[$date])) {
                $days[$date] = [
                    'date' => $date,
                    'calories' => 0.0,
                    'records' => [],
                ];
            }

            $item = $record->toDiaryArray();
            $days[$date]['records'][] = $item;
            $days[$date]['calories'] += $item['meal']['calories'] ?? 0;
        }

        return array_values($days);
    }

    public function toDiaryArray(): array
    {
        $type = $this->resolvedType();
        $item = [
            'id' => $this->id,
            'type' => $type,
            'timestamp' => $this->timestamp->format('Y-m-d H:i:s'),
            'comment' => $this->comment,
        ];

        if ($type === DiaryRecordType::GLUCOSE->value) {
            $item['glucose'] = (float) ($this->glucose->first()?->gl ?? 0);
        }

        if ($type === DiaryRecordType::MEAL->value) {
            $item['meal'] = $this->meals->first()?->toDiaryArray();
        }

        return $item;
    }

    protected function resolvedType(): int
    {
        if ($this->meals->isNotEmpty()) {
            return DiaryRecordType::MEAL->value;
        }

        if ($this->glucose->isNotEmpty()) {
            return DiaryRecordType::GLUCOSE->value;
        }

        return DiaryRecordType::COMMENT->value;
    }
}
