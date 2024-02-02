<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(Tests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

//

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

dataset('saques', function () {
    return [
        // Considerando que teremos notas suficientes de todos os valores
        [
            ['notasCem' => 30, 'notasCinquenta' => 10, 'notasVinte' => 50, 'notasDez' => 100],
            30,
            ['notasCem' => 0, 'notasCinquenta' => 0, 'notasVinte' => 1, 'notasDez' => 1],
        ],
        [
            ['notasCem' => 30, 'notasCinquenta' => 10, 'notasVinte' => 50, 'notasDez' => 100],
            80,
            ['notasCem' => 0, 'notasCinquenta' => 1, 'notasVinte' => 1, 'notasDez' => 1],
        ],
        [
            ['notasCem' => 30, 'notasCinquenta' => 10, 'notasVinte' => 50, 'notasDez' => 100],
            100,
            ['notasCem' => 1, 'notasCinquenta' => 0, 'notasVinte' => 0, 'notasDez' => 0],
        ],

        // Considerando que não temos notas de 20
        [
            ['notasCem' => 30, 'notasCinquenta' => 10, 'notasVinte' => 0, 'notasDez' => 100],
            30,
            ['notasCem' => 0, 'notasCinquenta' => 0, 'notasVinte' => 0, 'notasDez' => 3],
        ],
        [
            ['notasCem' => 30, 'notasCinquenta' => 10, 'notasVinte' => 0, 'notasDez' => 100],
            80,
            ['notasCem' => 0, 'notasCinquenta' => 1, 'notasVinte' => 0, 'notasDez' => 3],
        ],
    ];
});
