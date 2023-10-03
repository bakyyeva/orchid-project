<?php

namespace App\Orchid\Layouts;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Fields\Upload;
use Orchid\Screen\Layouts\Rows;
use Orchid\Support\Facades\Layout;

class TaskCreateOrUpdateLayout extends Rows
{
    /**
     * Used to create the title of a group of form elements.
     *
     * @var string|null
     */
    protected $title;

    /**
     * Get the fields elements to be displayed.
     *
     * @return Field[]
     */
    protected function fields(): iterable
    {
        return [


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


        ];
    }
}
