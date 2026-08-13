<?php

namespace App\Classes\Diacalc;

class ProductsFromJson
{
    protected array $groups;
    public function __construct(array $struct)
    {
        $this->groups = array_map(fn($r, $id) => [
            'name'=> $r['gr_name'],
            'id'=> $id,
            'prods'=> array_map(fn($pr, $pr_id) => [
                'id' => $pr_id,
                'name' => $pr['name'],
                'prot' => $pr['prot'],
                'fat' => $pr['fat'],
                'carb' => $pr['carb'],
                'gi' => $pr['gi'],
            ], $r['prods'], array_keys($r['prods']))
        ], $struct['groups'], array_keys($struct['groups']));
    }

    public function getGroups():array
    {
        return $this->groups;
    }

    public function getGroup($id): array
    {
        if ($id >= 0 && $id < count($this->groups)) {
            return $this->groups[$id];
        }

        throw new \Exception('Group not found');
    }

    public function getProductCount($group_id)
    {
        $gr = $this->getGroup($group_id);

        return count($gr['prods']);
    }

    public function getProducts($group_id) {
        $gr = $this->getGroup($group_id);

        return $gr['prods'];
    }

    public function getProduct($group_id, $product_id)
    {
        $gr = $this->getGroup($group_id);

        if ($product_id >= 0 && $product_id < count($gr['prods'])) {
            return $gr['prods'][$product_id];
        }

        throw new \Exception('Product not found');
    }
}
