<?php

use function Pest\Laravel\postJson;

describe('abastecimento', function () {
    test('deve poder realizar um abastecimento', function () {
        postJson('/caixa/abastecer', [
            'caixaDisponivel' => true,
            'notas' => [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ])
            ->assertStatus(200)
            ->assertJson([
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ]
                ],
                'erros' => []
            ]);
    });

    test('deve poder realizar múltiplos abastecimentos sem disponibilizar o caixa', function () {
        postJson('/caixa/abastecer', [
            'caixaDisponivel' => false,
            'notas' => [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ])
            ->assertStatus(200)
            ->assertJson([
                'caixa' => [
                    'caixaDisponivel' => false,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ]
                ],
                'erros' => []
            ]);

        postJson('/caixa/abastecer', [
            'caixaDisponivel' => false,
            'notas' => [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ])
            ->assertStatus(200)
            ->assertJson([
                'caixa' => [
                    'caixaDisponivel' => false,
                    'notas' => [
                        'notasCem' => 60,
                        'notasCinquenta' => 20,
                        'notasVinte' => 100,
                        'notasDez' => 200,
                    ]
                ],
                'erros' => []
            ]);
    });

    test('deve retornar erro ao abastecer caso o caixa já esteja disponível', function () {
        postJson('/caixa/abastecer', [
            'caixaDisponivel' => true,
            'notas' => [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ]);

        postJson('/caixa/abastecer', [
            'caixaDisponivel' => true,
            'notas' => [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ])
            ->assertStatus(400)
            ->assertJson([
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ]
                ],
                'erros' => ['caixa-em-uso']
            ]);
    });
});

describe('saque', function () {
    test('deve retornar erro caso o caixa esteja indisponível', function () {
        postJson('/caixa/sacar', [
            'valor' => 100,
            'horario' => now()
        ])
            ->assertStatus(400)
            ->assertJson([
                'caixa' => [],
                'erros' => ['caixa-inexistente']
            ]);
    });

    test('deve realizar saques com a menor quantidade de notas possivel', function (array $notas, int $valor, array $esperado) {
        postJson('/caixa/abastecer', [
            'caixaDisponivel' => true,
            'notas' => $notas
        ]);

        postJson('/caixa/sacar', [
            'valor' => $valor,
            'horario' => now()
        ])
            ->assertStatus(200)
            ->assertJson([
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => $notas['notasCem'] - $esperado['notasCem'],
                        'notasCinquenta' => $notas['notasCinquenta'] - $esperado['notasCinquenta'],
                        'notasVinte' => $notas['notasVinte'] - $esperado['notasVinte'],
                        'notasDez' => $notas['notasDez'] - $esperado['notasDez'],
                    ]
                ],
                'erros' => []
            ]);
    })->with('saques');

    test('deve retornar erro caso ocorra um saque maior que o saldo', function () {
        postJson('/caixa/abastecer', [
            'caixaDisponivel' => true,
            'notas' => [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ]);

        postJson('/caixa/sacar', [
            'valor' => 10000,
            'horario' => now()
        ])
            ->assertStatus(400)
            ->assertJson([
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ]
                ],
                'erros' => ['valor-indisponivel']
            ]);
    });

    test('deve retornar erro caso não tenha notas suficientes para o saque', function () {
        postJson('/caixa/abastecer', [
            'caixaDisponivel' => true,
            'notas' => [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 0,
                'notasDez' => 0,
            ]
        ]);

        postJson('/caixa/sacar', [
            'valor' => 30,
            'horario' => now()
        ])
            ->assertStatus(400)
            ->assertJson([
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 0,
                        'notasDez' => 0,
                    ]
                ],
                'erros' => ['valor-indisponivel']
            ]);
    });

    test('deve retornar erro caso haja dois saques de mesmo valor em menos de 10 minutos', function () {
        postJson('/caixa/abastecer', [
            'caixaDisponivel' => true,
            'notas' => [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ]);

        postJson('/caixa/sacar', [
            'valor' => 100,
            'horario' => now()->subMinutes(5),
        ]);

        postJson('/caixa/sacar', [
            'valor' => 100,
            'horario' => now(),
        ])
            ->assertStatus(400)
            ->assertJson([
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 29, // 30 - 1 por causa do primeiro saque
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ]
                ],
                'erros' => ['saque-duplicado']
            ]);
    });

    test('deve permitir o saque de mesmo valor se passados 10 minutos', function () {
        postJson('/caixa/abastecer', [
            'caixaDisponivel' => true,
            'notas' => [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ]);

        postJson('/caixa/sacar', [
            'valor' => 100,
            'horario' => now()->subMinutes(15),
        ]);

        postJson('/caixa/sacar', [
            'valor' => 100,
            'horario' => now(),
        ])
            ->assertStatus(200)
            ->assertJson([
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 28, // 30 - 2 pois os dois saques foram bem sucedidos
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ]
                ],
                'erros' => []
            ]);
    });
});
