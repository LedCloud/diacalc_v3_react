<?php

namespace App\Http\Controllers;

use App\Http\Requests\FactorsPatchRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class FactorsController extends Controller
{
    public function index()
    {
        $settings = Auth::user()->getSetting('User');
        $factors = Auth::user()->factors;

        return Inertia::render('Factors',[
            'factors' => $factors,
            'settings' => $settings,
        ]);
    }

    public function update(FactorsPatchRequest $request)
    {
        $validated = $request->validated();
        //split the result to factors and settings
        $factors = $validated['factors'];
        $settings = $validated;
        unset($settings['factors']);

        $settings = array_merge(Auth::user()->getSetting('User'), $settings);
        Auth::user()->putSetting('User', $settings);

        $old_factors = array_filter($factors, fn($e) => $e['id'] > 0);
        $old_update_ids = !empty($old_factors) ? array_column($old_factors, 'id') : [];
        Auth::user()->factors()->whereNotIn('id', $old_update_ids)->delete();
        Auth::user()->factors()->upsert($old_factors,
            uniqueBy: ['id']
        );

        $new_factors = array_filter($factors, fn($e) => $e['id'] < 0);
        Auth::user()->factors()->createMany($new_factors);

        session()->flash('notification', 'Updated');

        return Redirect::route('factors');
    }
}
