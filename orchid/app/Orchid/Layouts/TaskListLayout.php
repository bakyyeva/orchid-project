<?php

namespace App\Orchid\Layouts;

use App\Models\Task;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class TaskListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'tasks';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id')->sort(),
            TD::make('name')
                ->filter()
                ->render(function (Task $task) {
                    return ModalToggle::make($task->name)->modal('taskModal')->method('update', ['taskId' => $task->id])->asyncParameters(['taskId' => $task->id]);
                    // return Link::make($task->name)
                    //     ->route('task.edit', $task);
                }),
            TD::make('description')
                ->render(function (Task $task) {
                    //return Link::make('@@@@')
                    return Link::make($task->description)
                        ->route('task.edit', ['taskId' => $task->id]);
                }),

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
        ];
    }
}
