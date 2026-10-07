<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Math\Matrix;
use App\Exceptions\MatrixException;

class MatrixTest extends TestCase
{
    // --- CASOS FELIZES ---

    public function testSomaMatrizesValidas(): void
    {
        $m1 = new Matrix([[1, 2], [3, 4]]);
        $m2 = new Matrix([[5, 6], [7, 8]]);
        $res = $m1->add($m2);

        $this->assertEquals([[6, 8], [10, 12]], $res->getData());
    }

    public function testMultiplicacaoMatrizesValidas(): void
    {
        $m1 = new Matrix([[1, 2], [3, 4]]);
        $m2 = new Matrix([[2, 0], [1, 2]]);
        $res = $m1->multiply($m2);

        $this->assertEquals([[4, 4], [10, 8]], $res->getData());
    }

    public function testTransposicao(): void
    {
        $m = new Matrix([[1, 2, 3], [4, 5, 6]]);
        $res = $m->transpose();

        $this->assertEquals([[1, 4], [2, 5], [3, 6]], $res->getData());
    }

    public function testDeterminante2x2E3x3(): void
    {
        $m2x2 = new Matrix([[4, 6], [3, 8]]);
        $this->assertEqualsWithDelta(14.0, $m2x2->determinant(), 0.0001);

        $m3x3 = new Matrix([
            [6, 1, 1],
            [4, -2, 5],
            [2, 8, 7]
        ]);
        $this->assertEqualsWithDelta(-306.0, $m3x3->determinant(), 0.0001);
    }

    public function testSistemaLinearValido(): void
    {
        // 2x + y = 5
        // x + 3y = 10 -> Solução: x = 1, y = 3
        $A = new Matrix([[2, 1], [1, 3]]);
        $B = [5, 10];

        $solucao = $A->solveLinearSystem($B);

        $this->assertEqualsWithDelta(1.0, $solucao[0], 0.0001);
        $this->assertEqualsWithDelta(3.0, $solucao[1], 0.0001);
    }

    // --- CASOS DE BORDA ---

    public function testMatriz1x1(): void
    {
        $m = new Matrix([[5]]);
        $this->assertEquals(5.0, $m->determinant());
        $this->assertEquals([[5]], $m->transpose()->getData());
    }

    public function testMatrizIdentidade(): void
    {
        $I = new Matrix([[1, 0], [0, 1]]);
        $A = new Matrix([[3, 7], [2, 5]]);
        $res = $A->multiply($I);

        $this->assertEquals($A->getData(), $res->getData());
        $this->assertEqualsWithDelta(1.0, $I->determinant(), 0.0001);
    }

    public function testMatrizNula(): void
    {
        $zero = new Matrix([[0, 0], [0, 0]]);
        $A = new Matrix([[3, 7], [2, 5]]);
        $res = $A->add($zero);

        $this->assertEquals($A->getData(), $res->getData());
        $this->assertEqualsWithDelta(0.0, $zero->determinant(), 0.0001);
    }

    // --- CASOS DE ERRO ---

    public function testErroSomaDimensoesIncompativeis(): void
    {
        $this->expectException(MatrixException::class);
        $m1 = new Matrix([[1, 2]]);
        $m2 = new Matrix([[1], [2]]);
        $m1->add($m2);
    }

    public function testErroMultiplicacaoDimensoesIncompativeis(): void
    {
        $this->expectException(MatrixException::class);
        $m1 = new Matrix([[1, 2, 3]]);
        $m2 = new Matrix([[1, 2], [3, 4]]);
        $m1->multiply($m2);
    }

    public function testErroMatrizSingularSistemaLinear(): void
    {
        $this->expectException(MatrixException::class);
        // Linhas proporcionais -> Determinate = 0
        $A = new Matrix([[1, 2], [2, 4]]);
        $B = [3, 6];
        $A->solveLinearSystem($B);
    }
}