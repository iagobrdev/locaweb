<?php

use App\Data\Abastecimento;
use App\Data\Saque;
use App\Exceptions\CaixaEmUsoException;
use App\Exceptions\CaixaInexistenteException;
use App\Exceptions\SaqueDuplicadoException;
use App\Exceptions\ValorIndisponivelException;
use App\Services\Caixa;

describe('abastecimento', function () {
    test('deve poder realizar um abastecimento', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));
    })->throwsNoExceptions();

    test('deve poder realizar múltiplos abastecimentos sem disponibilizar o caixa', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: false,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: false,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));
    })->throwsNoExceptions();

    test('deve lançar uma exceção ao abastecer caso o caixa já esteja disponível', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));
    })->throws(CaixaEmUsoException::class);

    test('deve ignorar notas não aceitas', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
                'duzentos' => 10,
            ]
        ));

        expect($caixa->saldo())->toBe(5500);
    });

    test('deve calcular o saldo corretamente depois de um abastecimento', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));

        expect($caixa->saldo())->toBe(5500);
    });

    test('deve calcular o saldo corretamente depois de múltiplos abastecimentos', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: false,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));

        expect($caixa->saldo())->toBe(11000);
    });
});

describe('saque', function () {
    test('deve lançar uma exceção caso o caixa esteja indisponível', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->sacar(new Saque(
            valor: 100,
            horario: now(),
        ));
    })->throws(CaixaInexistenteException::class);

    test('deve realizar saques com a menor quantidade de notas possivel', function (array $notas, int $valor, array $esperado) {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: $notas
        ));

        $notas = $caixa->sacar(new Saque(
            valor: $valor,
            horario: now(),
        ));

        expect($notas)->toBe($esperado);
    })->with('saques');

    test('deve lançar uma exceção caso ocorra um saque maior que o saldo', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));

        $caixa->sacar(new Saque(
            valor: 10000,
            horario: now(),
        ));
    })->throws(ValorIndisponivelException::class);

    test('deve lançar uma exceção caso não tenha notas suficientes para o saque', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 0,
                'notasDez' => 0,
            ]
        ));

        $caixa->sacar(new Saque(
            valor: 30,
            horario: now(),
        ));
    })->throws(ValorIndisponivelException::class);

    test('deve lançar uma exceção caso haja dois saques de mesmo valor em menos de 10 minutos', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));

        $caixa->sacar(new Saque(
            valor: 100,
            horario: now()->subMinutes(5),
        ));

        $caixa->sacar(new Saque(
            valor: 100,
            horario: now(),
        ));
    })->throws(SaqueDuplicadoException::class);

    test('deve permitir o saque de mesmo valor se passados 10 minutos', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));

        $caixa->sacar(new Saque(
            valor: 100,
            horario: now()->subMinutes(15),
        ));

        $caixa->sacar(new Saque(
            valor: 100,
            horario: now(),
        ));
    })->throwsNoExceptions();

    test('deve calcular o saldo corretamente depois de um saque', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));

        $caixa->sacar(new Saque(
            valor: 100,
            horario: now(),
        ));

        expect($caixa->saldo())->toBe(5400);
    });

    test('deve calcular o saldo corretamente depois de múltiplos saques', function () {
        $caixa = app()->make(Caixa::class);

        $caixa->abastecer(new Abastecimento(
            caixaDisponivel: true,
            notas: [
                'notasCem' => 30,
                'notasCinquenta' => 10,
                'notasVinte' => 50,
                'notasDez' => 100,
            ]
        ));

        $caixa->sacar(new Saque(
            valor: 100,
            horario: now(),
        ));

        $caixa->sacar(new Saque(
            valor: 400,
            horario: now(),
        ));

        expect($caixa->saldo())->toBe(5000);
    });
});
