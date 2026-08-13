<?php

namespace App\Http\Controllers;

use App\Models\ArcGroup;
use App\Models\ArcProduct;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ArchiveController extends Controller
{
    public function index()
    {
        $groups_m = ArcGroup::all();
        $groups = array_map(fn($g) => ['id'=>$g['id'], 'name'=>$g['name']], $groups_m->toArray());

        return Inertia::render('Archive', [
            'groups' => $groups,
            'productGroups' => auth()->user()->productGroups()
                ->orderBy('sort_order')
                ->get(['id', 'name']),
        ]);
    }

    public function getProducts($groupId)
    {
        $products = ArcProduct::where('group_id', $groupId)->get();

        return response()->json($products);
    }

    public function addToProducts(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prot' => 'numeric|min:0',
            'fat' => 'numeric|min:0',
            'carb' => 'numeric|min:0',
            'gi' => 'integer|min:0|max:255',
            'product_group_id' => 'required|integer',
        ]);

        $group = auth()->user()->productGroups()
            ->whereKey($validated['product_group_id'])
            ->firstOrFail();

        $group->products()->create([
            'name' => $validated['name'],
            'prot' => $validated['prot'],
            'fat' => $validated['fat'],
            'carb' => $validated['carb'],
            'gi' => $validated['gi'],
            'weight' => 100,
        ]);

        return redirect()->back();
    }
}
