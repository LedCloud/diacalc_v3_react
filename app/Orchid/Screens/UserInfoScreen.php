<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
//use App\Models\Task;
use Illuminate\Http\Request;

class UserInfoScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            [
                'id' => 1,
                'name' => fake()->name(),
                'lastvisited' => fake()->dateTime(),
                'product_count' => rand(10, 1000),
            ],
            [
                'id' => 2,
                'name' => fake()->name(),
                'lastvisited' => fake()->dateTime(),
                'product_count' => rand(10, 1000),
            ],
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'UserInfo';
    }

    public function description(): ?string
    {
        return 'UserInfoDescr';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add Task')
                ->modal('taskModal')
                ->method('create')
                ->icon('plus'),
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function create(Request $request)
    {
        // Validate form data, save task to database, etc.
        $request->validate([
            'task.name' => 'required|max:255',
        ]);

        /*$task = new Task();
        $task->name = $request->input('task.name');
        $task->save();*/

    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::modal('userinfoModal', Layout::rows([
                Input::make('userinfo.name')
                    ->title('Name')
                    ->placeholder('Enter name')
                    ->help('The name of the user to be created/updated.'),
                Input::make('userinfo.email')
                    ->title('Email')
                    ->placeholder('Enter email')
                    ->help('The email of the user to be created/updated.'),
            ]))
                ->title('Create userinfo')
                ->applyButton('Add userinfo'),
        ];
    }
}
