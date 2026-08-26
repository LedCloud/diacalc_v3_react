<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class DiaryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated();

        $end = Carbon::parse($validated['end'])->startOfDay();
        $start = Carbon::parse($validated['start'])->startOfDay();

        if ($start->greaterThan($end)) {
            $start = $end->copy();
        }

        $range = [
            'start' => $start,
            'end' => $end,
        ];

        if ($key === null) {
            return $range;
        }

        return $range[$key] ?? $default;
    }
}
