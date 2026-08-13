<?php

namespace App\Orchid\Layouts;

use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

class SubtractListener extends Listener
{
    public function __construct(public $count)
    {
        for($i=0; $i<$this->count; $i++) {
            $this->targets[] = 'minuend.'. $i;
            $this->targets[] = 'subtrahend.'. $i;
        }
    }

    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = [];

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    protected function layouts(): iterable
    {
        $rows = [];
        for($i=0; $i<$this->count; $i++) {
            $rows[] = [
                Input::make('minuend.'. $i)
                    ->title('First argument')
                    ->type('number'),

                Input::make('subtrahend.' . $i)
                    ->title('Second argument')
                    ->type('number'),

                Input::make('result.'. $i)
                    ->readonly(),
                ];
        }
        return [
            Layout::rows($rows),
        ];
    }

    /**
     * Update state
     *
     * @param \Orchid\Screen\Repository $repository
     * @param \Illuminate\Http\Request  $request
     *
     * @return \Orchid\Screen\Repository
     */
    public function handle(Repository $repository, Request $request): Repository
    {
        $minuend = $request->input('minuend');
        $subtrahend = $request->input('subtrahend');

        return $repository
            ->set('minuend', $minuend)
            ->set('subtrahend', $subtrahend)
            ->set('result', $minuend - $subtrahend);
    }
}
