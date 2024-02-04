<?php

use function Pest\Laravel\postJson;

describe('abastecimento', function () {
    test('deve poder realizar um abastecimento', function () {
        postJson('/caixa', [
            [
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ],
                ],
            ],
        ])
            ->assertStatus(200)
            ->assertJson([
                [
                    'caixa' => [
                        'caixaDisponivel' => true,
                        'notas' => [
                            'notasCem' => 30,
                            'notasCinquenta' => 10,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                    'erros' => [],
                ],
            ]);
    });

    test('deve poder realizar múltiplos abastecimentos', function () {
        postJson('/caixa', [
            [
                'caixa' => [
                    'caixaDisponivel' => false,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ],
                ],
            ],
            [
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ],
                ],
            ],
        ])
            ->assertStatus(200)
            ->assertJson([
                [
                    'caixa' => [
                        'caixaDisponivel' => false,
                        'notas' => [
                            'notasCem' => 30,
                            'notasCinquenta' => 10,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                    'erros' => [],
                ],
                [
                    'caixa' => [
                        'caixaDisponivel' => true,
                        'notas' => [
                            'notasCem' => 60,
                            'notasCinquenta' => 20,
                            'notasVinte' => 100,
                            'notasDez' => 200,
                        ],
                    ],
                    'erros' => [],
                ],
            ]);
    });

    test('deve retornar erro ao abastecer um caixa já disponível', function () {
        postJson('/caixa', [
            [
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ],
                ],
            ],
            [
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ],
                ],
            ],
        ])
            ->assertStatus(200)
            ->assertJson([
                [
                    'caixa' => [
                        'caixaDisponivel' => true,
                        'notas' => [
                            'notasCem' => 30,
                            'notasCinquenta' => 10,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                    'erros' => [],
                ],
                [
                    'caixa' => [],
                    'erros' => ['caixa-em-uso'],
                ],
            ]);
    });
});

describe('saque', function () {
    test('deve retornar erro ao sacar de um caixa não disponível', function () {
        postJson('/caixa', [
            [
                'caixa' => [
                    'caixaDisponivel' => false,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ],
                ],
            ],
            [
                'saque' => [
                    'valor' => 100,
                    'horario' => now(),
                ],
            ],
        ])
            ->assertStatus(200)
            ->assertJson([
                [
                    'caixa' => [
                        'caixaDisponivel' => false,
                        'notas' => [
                            'notasCem' => 30,
                            'notasCinquenta' => 10,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                    'erros' => [],
                ],
                [
                    'caixa' => [],
                    'erros' => ['caixa-inexistente'],
                ],
            ]);
    });

    test('deve poder realizar múltiplos saques de valores diferentes', function () {
        postJson('/caixa', [
            [
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ],
                ],
            ],
            [
                'saque' => [
                    'valor' => 100,
                    'horario' => now(),
                ],
            ],
            [
                'saque' => [
                    'valor' => 50,
                    'horario' => now(),
                ],
            ],
        ])
            ->assertStatus(200)
            ->assertJson([
                [
                    'caixa' => [
                        'caixaDisponivel' => true,
                        'notas' => [
                            'notasCem' => 30,
                            'notasCinquenta' => 10,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                ],
                [
                    'caixa' => [
                        'caixaDisponivel' => true,
                        'notas' => [
                            'notasCem' => 29,
                            'notasCinquenta' => 10,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                ],
                [
                    'caixa' => [
                        'caixaDisponivel' => true,
                        'notas' => [
                            'notasCem' => 29,
                            'notasCinquenta' => 9,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                ],
            ]);
    });

    test('deve retornar erro ao realizar dois saques de mesmo valor em menos de 10 minutos', function () {
        postJson('/caixa', [
            [
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ],
                ],
            ],
            [
                'saque' => [
                    'valor' => 100,
                    'horario' => now()->subMinutes(5),
                ],
            ],
            [
                'saque' => [
                    'valor' => 100,
                    'horario' => now(),
                ],
            ],
        ])
            ->assertStatus(200)
            ->assertJson([
                [
                    'caixa' => [
                        'caixaDisponivel' => true,
                        'notas' => [
                            'notasCem' => 30,
                            'notasCinquenta' => 10,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                ],
                [
                    'caixa' => [
                        'caixaDisponivel' => true,
                        'notas' => [
                            'notasCem' => 29,
                            'notasCinquenta' => 10,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                ],
                [
                    'caixa' => [],
                    'erros' => ['saque-duplicado'],
                ],
            ]);
    });

    test('deve poder sacar o mesmo valor se passados 10 minutos', function () {
        postJson('/caixa', [
            [
                'caixa' => [
                    'caixaDisponivel' => true,
                    'notas' => [
                        'notasCem' => 30,
                        'notasCinquenta' => 10,
                        'notasVinte' => 50,
                        'notasDez' => 100,
                    ],
                ],
            ],
            [
                'saque' => [
                    'valor' => 100,
                    'horario' => now()->subMinutes(15),
                ],
            ],
            [
                'saque' => [
                    'valor' => 100,
                    'horario' => now(),
                ],
            ],
        ])
            ->assertStatus(200)
            ->assertJson([
                [
                    'caixa' => [
                        'caixaDisponivel' => true,
                        'notas' => [
                            'notasCem' => 30,
                            'notasCinquenta' => 10,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                ],
                [
                    'caixa' => [
                        'caixaDisponivel' => true,
                        'notas' => [
                            'notasCem' => 29,
                            'notasCinquenta' => 10,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                ],
                [
                    'caixa' => [
                        'caixaDisponivel' => true,
                        'notas' => [
                            'notasCem' => 28,
                            'notasCinquenta' => 10,
                            'notasVinte' => 50,
                            'notasDez' => 100,
                        ],
                    ],
                ],
            ]);
    });
});
