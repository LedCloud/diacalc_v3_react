<?php

namespace App\Orchid\Layouts\Archive;

use App\Models\ArcProduct;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;
class ArcProductListLayout extends Table
{
    protected $target = 'products';

    public function columns(): array
    {
        return [
            TD::make('name')
            ->sort()
            ->filter(Input::make()),
            TD::make('prot')
            ->sort()
            ->filter(Input::make()),
            TD::make('fat')
            ->sort()
            ->filter(Input::make()),
            TD::make('carb')
            ->sort()
            ->filter(Input::make()),
            TD::make('gi')
            ->sort()
            ->filter(Input::make()),

            TD::make('id', __('Actions'))
                ->cantHide()
                ->align(TD::ALIGN_CENTER)
                ->width('5%')
                ->render(function(ArcProduct $product){
                    return DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.archive.product.edit', $product->id)
                                ->icon('bs.pencil'),

                            Button::make(__('Delete'))
                                ->icon('bs.trash')
                                ->confirm(__('This product will be deleted'))
                                ->method('delete', [
                                    'group_id' => $product->id,
                                ]),
                        ]);
                }),
        ];
    }
}
