<?php

namespace App\Orchid\Screens;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Orchid\Layouts\SumListener;
use App\Orchid\Layouts\TaskCreateOrUpdateLayout;
use App\Orchid\Layouts\TaskListLayout;
use Orchid\Attachment\File;
use Orchid\Attachment\Models\Attachment;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Attach;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Cropper;
use Orchid\Screen\Fields\Input;
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
        $tasks = Task::filters()->defaultSort('id')->paginate(5);
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
     * Permission
     *
     * @return iterable|null
     */
    public function permission(): ?iterable
    {
        return [
            'platform.tasks'
        ];
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
                ->class('btn btn-success'),

            //ModalToggle::make('Task Güncelle')
            //->modal('editTask')
            //->method('update')
            //->icon('pencil')
            //->class('btn btn-primary'),
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
            Layout::modal('taskModal', TaskCreateOrUpdateLayout::class)
                ->title('Task Oluştur')
                ->applyButton('TaskEkle')->async('asyncGetData'),

            TaskListLayout::class,

        ];
    }

    public function asyncGetData($taskId = null): array
    {
        if ($taskId) {
            $task = Task::find($taskId);
            $task->image = json_decode($task->image, true);
        } else {
            $task = new Task();
        }
        return [
            'task' => $task,
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
            ->fill(['image' => json_encode($request->get('task')['image'])]);
        // ->fill(['image' => $request->has('task.image') ? $request->get('task')['image'][0] : null]);
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
}
