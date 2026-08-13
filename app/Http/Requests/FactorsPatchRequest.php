<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FactorsPatchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'factors_by_time' => ['string', Rule::in('timed')],
            'weight' => ['numeric', 'min:1', 'integer'],
            'k3_factor' => 'numeric|min:1|integer',
            'be' => 'numeric|min:1|integer',
            'factors.*.id' => ['numeric', 'integer'],
            'factors.*.time' => [Rule::date()->format('H:i')],
            'factors.*.k1' => 'numeric|min:0.01',
            'factors.*.k2' => 'numeric|min:0',
            'factors.*.k3' => 'numeric|min:0.01',
            'factors.*.ouv' => 'numeric|min:0.01',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if (isset($this->factors_by_time)) {
            $validated['factors_by_time'] = (int)($this->factors_by_time === 'timed');
        } else {
            $validated['factors_by_time'] = 0;
        }

        if (!empty($validated['factors'])) {
            $validated['factors'] = array_map(function ($e) {
                unset($e['ouv']);
                return $e;
            }, $validated['factors']);
        } else {
            $validated['factors'] = [];
        }

        return $validated;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'factors.*.time.date_format' => 'The time must be in HH:MM format.',
            'factors.*.k1.min' => 'The value must be numeric and positive',
            'factors.*.k2.min' => 'The value must be numeric and positive',
            'factors.*.ouv.min' => 'The value must be numeric and positive',
        ];
    }
}
