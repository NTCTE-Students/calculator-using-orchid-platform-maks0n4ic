<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\TD;

class CalculatorScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'history' => session()->get('history', []),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Калькулятор';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): array
    {
        return [
            Button::make('Считать')
                ->method('calculate')
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
                Input::make('a')
                    ->type('number')
                    ->title('Первое число'),
                Select::make('operation')
                    ->options([
                        '+' => '+',
                        '-' => '-',
                        '*' => '*',
                        '/' => '/',
                        '%' => 'Остаток от деления',
                        '//' => 'Целочисленное деление',
                        '**' => 'Возведение в степень',
                        '√' => 'Квадратный корень',
                        'log' => 'Логарифм',
                        'sin' => 'Синус',
                        'cos' => 'Косинус',
                        'tan' => 'Тангенс',
                    ])
                    ->title('Операция'),
                Input::make('b')
                    ->type('number')
                    ->title('Второе число'),
            ]),
            Layout::table('history', [
                TD::make('a', 'Действие')
                    -> render(fn($operation) => $operation['a'] . $operation['operation'] . $operation['b']),
                TD::make('result', 'Результат')
                    -> render(fn($operation) => $operation['result']),
            ]),
        ];
    }

    public function calculate(Request $request)
    {
        $a = $request->get('a');
        $b = $request->get('b');
        $operation = $request->get('operation');
        $result = 0;

        switch ($operation) {
            case '+':
                $result = $a + $b;
                break;
            case '-':
                $result = $a - $b;
                break;
            case '*':
                $result = $a * $b;
                break;
            case '/':
                if ($b != 0) {
                    $result = $a / $b;
                } else {
                    return back()->withErrors(['error' => 'Деление на ноль невозможно']);
                }
                break;
            case '%':
                $result = $a % $b;
                break;
            case '//':
                $result = intdiv($a, $b);
                break;
            case '**':
                $result = $a ** $b;
                break;
            case '√':
                $result = sqrt($a);
                break;
            case 'log':
                $result = log($a, $b);
                break;
            case 'sin':
                $result = sin($a);
                break;
            case 'cos':
                $result = cos($a);
                break;
            case 'tan':
                $result = tan($a);
                break;
        }
        $request->session()->push('history', [
            'operation' => $operation,
            'a' => $a,
            'b' => $b,
            'result' => $result,
        ]);
        return back()->with('result', $result);
    }
}
