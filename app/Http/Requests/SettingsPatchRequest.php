<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingsPatchRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'menu_info' => ['numeric', 'integer', 'min:1', 'max:255'],
            'round_to' => ['numeric', Rule::in(['1', '2', '3']),],
            'calorie_limit' => ['numeric', 'integer', 'min:1200', ],

            'is_mmol' => ['numeric', Rule::in(['1', '0'])],
            'is_plasma' => ['numeric', Rule::in(['1', '0'])],
            'target' => ['numeric', 'min:3'],
            'low_level' => ['numeric', 'min:3'],
            'high_level' => ['numeric', 'min:3'],

            'fillDefault' => ['string', Rule::in('fill')],
            'use_freq' => ['string', 'nullable', Rule::in('use')],
            'freq_qty' => ['numeric', 'integer', 'min:0'],
            'filter_off'  => ['numeric', 'integer', 'min:0'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if (isset($this->is_mmol)) {
            $validated['is_mmol'] = (int)$this->is_mmol;
        } else {
            $validated['is_mmol'] = 0;
        }
        if (isset($this->is_plasma)) {
            $validated['is_plasma'] = (int)$this->is_plasma;
        } else {
            $validated['is_plasma'] = 0;
        }
        if (isset($this->use_freq)) {
            $validated['use_freq'] = !empty($this->use_freq) ? intval($this->use_freq === 'use') : 0;
        } else {
            $validated['use_freq'] = 0;
        }

        return $validated;
    }
}
