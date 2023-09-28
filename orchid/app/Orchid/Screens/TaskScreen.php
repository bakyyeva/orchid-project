<?php

namespace App\Orchid\Screens;

use Illuminate\Http\Request;
use App\Models\Task;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Picture;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Screen\TD;
use Orchid\Support\Facades\Layout;


class TaskScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        $tasks = Task::all();
        $tasks->each(function ($task) {
            $task->load('attachment');
        });

        return [
            'tasks' => $tasks,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Simple To Do list';
    }

    public function description(): ?string
    {
        return 'Description TO DO List Project';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Task ekle')
                ->modal('taskModal')
                ->method('create')
                ->icon('plus')
                ->class('btn btn-success')
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::modal('taskModal', Layout::rows([
                Input::make('task.name')
                    ->title('Name')
                    ->placeholder('Task girin'),

                TextArea::make('task.description')
                    ->title('Description')
                    ->placeholder('Task açıklama girin'),

                CheckBox::make('task.active')
                    ->value(1)
                    ->title('Status')
                    ->help('Task active olsunmu?'),

                Select::make('task.category')
                    ->options([
                        'okul' => 'Okul',
                        'iş' => 'İş',
                        'kişisel' => 'Kişisel',
                    ])
                    ->title('Kategory seçin')
                    ->empty('No select'),

                Upload::make('task.image')
                    ->title('Task resim')
                    ->maxFileSize(2),

            ]))->title('Task Oluştur')->applyButton('Task Ekle'),

            Layout::table('tasks', [
                TD::make('id'),
                TD::make('name'),
                TD::make('description'),
                TD::make('image')->render(function (Task $task) {
                    return $task->attachment->map(function ($attachment) {
                        return "<img src='{$attachment->url}' width='100px' />";
                    })->implode(' ');
                }),

                TD::make('Actions')
                    ->alignRight()
                    ->render(function (Task $task) {
                        return Button::make('Task sil')
                            ->class('btn btn-danger')
                            ->icon('trash')
                            ->confirm('After deleting, the task will be gone forever.')
                            ->method('delete', ['task' => $task->id]);
                    }),

            ])
        ];
    }

    public function create(Request $request)
    {
        $request->validate([
            'task.name' => 'required|max:255',
            'task.description' => 'required|min:8',
        ]);

        $isActive = $request->has('task.active') ? 1 : 0;
        $task = new Task();
        $task->fill(collect($request->get('task'))->except(['image', 'active'])->toArray())
            ->fill(['image' => $request->has('task.image') ? $request->get('task')['image'][0] : null]);
        $task->active = $isActive;
        $task->save();

        $task->attachment()->syncWithoutDetaching(
            $request->input('task.image', [])
        );
    }

    public function delete(Task $task)
    {
        $task->delete();
    }
}
