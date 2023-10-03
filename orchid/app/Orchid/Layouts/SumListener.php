<?php

namespace App\Orchid\Layouts;

use Illuminate\Http\Request;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Listener;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;

class SumListener extends Listener
{
    /**
     * List of field names for which values will be listened.
     *
     * @var string[]
     */
    protected $targets = [
        'numOne',
        'numTwo'
    ];

    // protected $asyncMethod = 'asyncSym';

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    protected function layouts(): iterable
    {
        return [
            Layout::rows([

                Input::make('numOne')
                    ->title('1.Sayı girin')
                    ->type('number'),

                Input::make('numTwo')
                    ->title('2.Sayıyı girin')
                    ->type('number'),

                Input::make('result')
                    ->readonly()
                    ->value($this->query->get('result'))

            ]),

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
        $numOne = $request->get('numOne');
        $numTwo = $request->get('numTwo');
        return $repository
            ->set('numOne', $numOne)
            ->set('numTwo', $numTwo)
            ->set('result', $numOne + $numTwo);
    }
}
