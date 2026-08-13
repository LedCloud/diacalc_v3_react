<?php

namespace App\Orchid\Layouts\Archive;

use App\Models\ArcGroup;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class ArcGroupListLayout extends Table
{
    protected $target = 'groups';

    public function columns(): array
    {
        return [
            TD::make('name', __('Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->width('80%')
                ->render(fn (ArcGroup $arcGroup) =>
                    Link::make($arcGroup->name . ':' . $arcGroup->id)
                    ->asyncRoute('asyncGetProducts', $arcGroup->id)
                )
            ,
                    //->route('platform.systems.archive.edit', $arcGroup->id))
            TD::make('product_count', __('Products'))
                ->render(fn (ArcGroup $group) => $group->arc_products()->count())
                ->sort()
                ->cantHide()
                ->width('10%')
                ->filter(Input::make()),

            TD::make('id', __('Actions'))
                ->cantHide()
                ->align(TD::ALIGN_CENTER)
                ->width('5%')
                ->render(function(ArcGroup $group){
                    return DropDown::make()
                        ->icon('bs.three-dots-vertical')
                        ->list([
                            Link::make(__('Edit'))
                                ->route('platform.archive.group.edit', $group->id)
                                ->icon('bs.pencil'),

                            Button::make(__('Delete'))
                                ->icon('bs.trash')
                                ->confirm(__('This group will be deleted with all products in it'))
                                ->method('delete', [
                                    'group_id' => $group->id,
                                ]),
                        ]);
                }),
        ];
    }
}
