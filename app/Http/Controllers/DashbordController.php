<?php

namespace App\Http\Controllers;

use App\Classes\Settings\MenuInfo;
use App\Models\Menu;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Services\CalculateFactorsService;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashbordController
{
    public function updateFactors(Request $request)
    {
        //{"factor":{"k1":1.49,"k2":0.28,"k3":1.33929,"gl1":10.7143,"gl2":5.35714,"be":12}}
        $validated = $request->validate([
            'factor' => 'required|array',
            'factor.k1' => 'numeric|min:0.01',
            'factor.k2' => 'numeric|min:0',
            'factor.k3' => 'numeric|min:0.01',
            'factor.gl1' => 'numeric|min:0.1',
            'factor.gl2' => 'numeric|min:0.1',
            'factor.be' => 'numeric|min:0.1',
        ]);

        $factor = $validated['factor'];

        auth()->user()->eating->update(
            collect($factor)->only(['k1', 'k2', 'k3', 'gl1', 'gl2'])->all()
        );

        $setting = auth()->user()->getSetting('User');
        $setting['be'] = $validated['factor']['be'];
        auth()->user()->putSetting('User', $setting);

        Log::info('Validated', $validated);

        return redirect()->back();
    }
    public function deleteitem(Menu $menu)
    {
        // Ensure the menu belongs to the current user
        if ($menu->user_id !== auth()->id()) {
            abort(403);
        }

        $menu->delete();

        return redirect()->back();
    }

    public function update(Request $request)
    {
        Log::info('req', $request->all());

        $validated = $request->validate([
            'menu_items' => 'required|array',
            'menu_items.*.id' => 'numeric|min:1|integer',
            'menu_items.*.weight' => 'numeric|min:0|integer',
        ]);

        $db_ids = auth()->user()->menus->pluck('id')->toArray();

        //$valid_collection = collect($validated['menu_items']);
        //$request_ids = $valid_collection->pluck('id');

//        $to_update = $db_ids->intersect($request_ids);
//        $to_delete = $db_ids->diff($request_ids);
//        $to_create = $request_ids->diff($db_ids);

        $to_update = array_column($validated['menu_items'], 'id');
        $to_delete = array_diff($db_ids, $to_update);
        auth()->user()->menus()->whereIn('id', $to_delete)->delete();
        foreach($validated['menu_items'] as $menu_data) {
            auth()->user()->menus()->where('id', $menu_data['id'])->update(['weight' => $menu_data['weight']]);
        }

        return redirect()->back();
    }
    public function moveGroup(Request $request, ProductGroup $group)
    {
        if ($group->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'direction' => 'required|in:up,down',
        ]);

        $groups = auth()->user()->productGroups()->get()->values();
        $ids = $groups->pluck('id')->all();
        $index = array_search($group->id, $ids, true);

        if ($index === false || count($ids) < 2) {
            return redirect()->back();
        }

        $direction = $validated['direction'];
        $count = count($ids);

        if ($direction === 'up') {
            if ($index === 0) {
                $item = array_shift($ids);
                $ids[] = $item;
            } else {
                [$ids[$index - 1], $ids[$index]] = [$ids[$index], $ids[$index - 1]];
            }
        } else {
            if ($index === $count - 1) {
                $item = array_pop($ids);
                array_unshift($ids, $item);
            } else {
                [$ids[$index], $ids[$index + 1]] = [$ids[$index + 1], $ids[$index]];
            }
        }

        foreach ($ids as $sortOrder => $id) {
            ProductGroup::where('id', $id)
                ->where('user_id', auth()->id())
                ->update(['sort_order' => $sortOrder + 1]);
        }

        return redirect()->back();
    }

    public function getProducts(int $group)
    {
        $fields = ['id', 'name', 'prot', 'fat', 'carb', 'gi'];

        if ($group === 0) {
            $setting = auth()->user()->getSetting('User');
            if ((int) ($setting['use_freq'] ?? 0) !== 1) {
                abort(404);
            }

            $groupIds = auth()->user()->productGroups()->pluck('id');
            $limit = max(0, (int) ($setting['freq_qty'] ?? 0));

            $products = Product::query()
                ->whereIn('product_group_id', $groupIds)
                ->orderByDesc('used')
                ->limit($limit)
                ->get($fields);

            return response()->json($products);
        }

        $productGroup = ProductGroup::query()
            ->where('id', $group)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $products = $productGroup->products()->orderBy('name')->get($fields);

        return response()->json($products);
    }

    public function searchProducts(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:3|max:255',
        ]);

        $like = '%' . addcslashes($validated['q'], '%_\\') . '%';
        $groupIds = auth()->user()->productGroups()->pluck('id');

        $products = Product::query()
            ->whereIn('product_group_id', $groupIds)
            ->where('name', 'like', $like)
            ->orderBy('name')
            ->limit(50)
            ->get(['id', 'name', 'product_group_id']);

        return response()->json($products);
    }

    public function addProductToMenu(Product $product)
    {
        $this->authorizeUserProduct($product);

        auth()->user()->menus()->create([
            'name' => $product->name,
            'prot' => $product->prot,
            'fat' => $product->fat,
            'carb' => $product->carb,
            'gi' => $product->gi,
            'weight' => 0,
        ]);

        return redirect()->back();
    }

    public function updateProduct(Request $request, Product $product)
    {
        $this->authorizeUserProduct($product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prot' => 'numeric|min:0',
            'fat' => 'numeric|min:0',
            'carb' => 'numeric|min:0',
            'gi' => 'integer|min:0|max:255',
        ]);

        $product->update($validated);

        return redirect()->back();
    }

    public function deleteProduct(Product $product)
    {
        $this->authorizeUserProduct($product);

        $product->delete();

        return redirect()->back();
    }

    protected function authorizeUserProduct(Product $product): void
    {
        $owned = Product::query()
            ->whereKey($product->id)
            ->whereHas('productGroup', fn ($q) => $q->where('user_id', auth()->id()))
            ->exists();

        if (!$owned) {
            abort(403);
        }
    }

    public function index()
    {
        $menus = auth()->user()->menus;
        $setting = auth()->user()->getSetting('User');

        if ($setting['factors_by_time'] && count(auth()->user()->factors)) {
            $factors = CalculateFactorsService::calculate(auth()->user()->factors);
        } else {
            $factors = auth()->user()->factors;
        }

        $masks = MenuInfo::getAllNamed();
        unset($masks['true'], $masks['false']);

        $groups = auth()->user()->productGroups()
            ->get(['id', 'name', 'sort_order'])
            ->map(fn (ProductGroup $group) => [
                'id' => $group->id,
                'name' => $group->name,
                'sort_order' => $group->sort_order,
                'virtual' => false,
            ])
            ->values()
            ->all();

        if ((int) ($setting['use_freq'] ?? 0) === 1) {
            array_unshift($groups, [
                'id' => 0,
                'name' => __('dashboard.freq_used'),
                'sort_order' => null,
                'virtual' => true,
            ]);
        }

        return Inertia::render('Dashboard',
            [
                'eating' => auth()->user()->eating,
                'menu_items' => $menus,
                'settings' => $setting,
                'menu_masks' => $masks,
                'factors' => $factors,
                'groups' => $groups,
            ]
        );
    }
}
