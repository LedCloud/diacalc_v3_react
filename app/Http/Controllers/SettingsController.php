<?php

namespace App\Http\Controllers;

use App\Classes\Settings\MenuInfo;
use App\Classes\Settings\UserSetting;
use App\Http\Requests\SettingsPatchRequest;
use App\Models\ProductGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
    {
        if (auth()) {
            $settings = Auth::user()->getSetting('User');
        } else {
            $settings = UserSetting::DEFAULT;
        }

        /*session()->flash('notification', 'Catch me');
        session()->flash('error', 'Catch an error');
        session()->flash('warning', 'Catch a warning too')*/;

        return Inertia::render('Settings',[
            'settings' => $settings,
            'menuMasks' => MenuInfo::getAllNamed(),
        ]);
    }

    public function update(SettingsPatchRequest $request)
    {
        $validated = $request->validated();

        $settings = array_merge(Auth::user()->getSetting('User'), $validated);

        Auth::user()->putSetting('User', $settings);

        session()->flash('notification', 'Settings saved');

        return Redirect::route('settings');
    }

    public function fillProducts()
    {
        auth()->user()->productGroups()->delete();
        $gr = \App\Models\ArcGroup::all();
        $sort_indx = 1;
        foreach ($gr as $g) {
            $group = ProductGroup::create([
                'name' => $g->name,
                'sort_order' => $sort_indx++,
                'user_id' => Auth::id(),
            ]);
            if ($group) {
                $products = $g->arc_products->map(function($p) use($group) {
                    return [
                        'name' => $p->name,
                        'prot' => $p->prot,
                        'fat' => $p->fat,
                        'carb' => $p->carb,
                        'gi' => $p->gi,
                        'weight' => 100,
                    ];
                });
                $group->products()->createMany($products);
            }
        }

        session()->flash('notification', 'Products created');

        return Redirect::route('settings');
    }
}

