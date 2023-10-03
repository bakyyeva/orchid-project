<?php

namespace App\Orchid\Screens;

use App\Models\Task;
use Illuminate\Http\Request;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class TaskEditScreen extends Screen
{

    public $task;
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Task $task, int $taskId): iterable
    {
        if ($taskId) {
            $task = Task::find($taskId);
            $task->image = json_decode($task->image, true);
        } 
        return [
            'task' => $task,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return  'Edit task';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [

            Button::make('Task Ekle')
                ->icon('pencil')
                ->class('btn btn-success')
                ->method('create')
                ->canSee(!$this->task->exists),

            Button::make('Task Güncelle')
                ->icon('note')
                ->class('btn btn-primary')
                ->method('update')
                ->canSee($this->task->exists),

            Button::make('Task Sil')
                ->icon('trash')
                ->class('btn btn-danger')
                ->method('delete')
                ->canSee($this->task->exists),

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

            Layout::rows([

                Input::make('task.id')
                    ->type('hidden'),

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
                    ->targetId()

            ])
        ];
    }

    public function update(Request $request)
    {
        $task = Task::find($request->taskId);
        $isActive = $request->has('task.active') ? 1 : 0;
        // if ($task->attachment()->count() > 0) {
        //     $task->attachment->each->delete();
        // }
        $images = $request->get('task')['image'];
        foreach ($images as $image) {
            $imageIds[] = $image;
        }
        $task->fill(collect($request->get('task'))->except(['image', 'active'])->toArray())
            ->fill(['image' => $request->has('task.image') ? $imageIds : null]);
        $task->active = $isActive;
        $task->save();

        $task->attachment()->syncWithoutDetaching(
            $request->input('task.image', [])
        );

        return redirect()->route('task');
    }

    public function delete()
    {
        $this->task->delete();

        return redirect()->route('task');
    }
}
