<?php

namespace App\Http\Controllers;

use App\Classes\Settings\MenuInfo;
use App\Classes\Settings\UserSetting;
use App\Http\Requests\DiaryRequest;
use App\Models\Diary;
use Inertia\Inertia;

class DiaryController extends Controller
{
    public function index()
    {
        $settings = auth()->user()?->getSetting('User') ?? UserSetting::DEFAULT;

        return Inertia::render('Diary', [
            'settings' => $settings,
            'menu_masks' => MenuInfo::getAllNamed(),
        ]);
    }

    public function records(DiaryRequest $request)
    {
        $start = $request->validated('start');
        $end = $request->validated('end');

        return response()->json([
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'diary' => Diary::daysForUserRange(auth()->id(), $start, $end),
        ]);
    }
}
