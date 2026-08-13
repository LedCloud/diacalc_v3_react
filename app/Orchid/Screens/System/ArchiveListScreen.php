<?php

namespace App\Orchid\Screens\System;

use App\Classes\Settings\ContactEmailsSetting;
use App\Classes\Settings\TelegramSetting;
use App\Models\ArcGroup;
use App\Models\ArcProduct;
use App\Orchid\Layouts\Archive\ArcGroupListLayout;
use App\Orchid\Layouts\Archive\ArcProductListLayout;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class ArchiveListScreen extends Screen
{
    protected $groupId = null;

    public function __construct()
    {
        $this->groupId = ArcGroup::first()->id;
    }


    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Product archive');
    }

    public function description(): ?string
    {
        return __('This product archive is sharable between all users');
    }

    public function commandBar(): iterable
    {
        return [
            /*Link::make(__('Add group'))
                ->icon('bs.plus-circle')
                ->href(route('platform.systems.roles.create')),
            Link::make(__('Add group'))
                ->icon('bs.plus-circle')
                ->href(route('platform.systems.roles.create')),*/
        ];
    }

    public function layout(): iterable
    {
        return [
            Layout::split([
                ArcGroupListLayout::class,
//                Layout::wrapper('orchid.wrapper_sample', [
//                    'products' => ArcProductListLayout::class->async('asyncGetProducts'),
//                ]),
                ArcProductListLayout::class,
            ])->ratio('40/60')
            //    ->title('Some title')
            ,
            ];
    }

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'groups' => ArcGroup::filters()->paginate(10, ['*'], 'group_page'),
            'products' =>ArcProduct::where('group_id', $this->groupId)->filters()->paginate(10, ['*'], 'product_page'),
        ];
    }
    public function asyncGetProducts(int $groupId)
    {
        return [
            'products' => ArcProduct::where('group_id', $groupId)->filters()->paginate(10, ['*'], 'product_page'),
        ];
    }
    public function delete($group_id)
    {
        $gr = ArcGroup::find($group_id);
        $name = $gr->name;
        $gr->delete();

        Toast::info(__("group $name deleted"));
    }
}
