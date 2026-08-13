<?php

namespace App\Orchid\Layouts\Products;

use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ProductListLayout extends Table
{
    protected $target = 'groups';

    public function columns(): iterable
    {
        return [
            TD::make('name'),
            TD::make('count'),
        ];
    }
}
