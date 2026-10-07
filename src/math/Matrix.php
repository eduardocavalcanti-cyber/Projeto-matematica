<?php

namespace App\Math;

use App\Exceptions\MatrixException;

class Matrix
{
    private array $data;
    private int $rows;
    private int $cols;

    public function __construct(array $data)
    {
        if (empty($data) || !is_array($data[0])) {
            throw new MatrixException("A matriz deve ser um array 2D não vazio.");
        }

        $this->rows = count($data);
        $this->cols = count($data[0]);

        foreach ($data as $row) {
            if (!is_array($row) || count($row) !== $this->cols) {
                throw new MatrixException("Todas as linhas da matriz devem ter o mesmo número de colunas.");
            }
            foreach ($row as $val) {
                if (!is_numeric($val)) {
                    throw new MatrixException("Todos os elementos da matriz devem ser numéricos.");
                }
            }
        }

        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getRows(): int
    {
        return $this->rows;
    }

    public function getCols(): int
    {
        return $this->cols;
    }

    // --- SOMA DE MATRIZES ---
    public function add(Matrix $other): Matrix
    {
        if ($this->rows !== $other->getRows() || $this->cols !== $other->getCols()) {
            throw new MatrixException("As dimensões das matrizes devem ser iguais para a soma.");
        }

        $result = [];
        $otherData = $other->getData();

        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {
                $result[$i][$j] = $this->data[$i][$j] + $otherData[$i][$j];
            }
        }

        return new Matrix($result);
    }

    // --- MULTIPLICAÇÃO DE MATRIZES ---
    public function multiply(Matrix $other): Matrix
    {
        if ($this->cols !== $other->getRows()) {
            throw new MatrixException("O número de colunas da primeira matriz deve ser igual ao número de linhas da segunda.");
        }

        $otherData = $other->getData();
        $otherCols = $other->getCols();
        $result = [];

        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $otherCols; $j++) {
                $sum = 0;
                for ($k = 0; $k < $this->cols; $k++) {
                    $sum += $this->data[$i][$k] * $otherData[$k][$j];
                }
                $result[$i][$j] = $sum;
            }
        }

        return new Matrix($result);
    }

    // --- TRANSPOSIÇÃO ---
    public function transpose(): Matrix
    {
        $result = [];
        for ($i = 0; $i < $this->rows; $i++) {
            for ($j = 0; $j < $this->cols; $j++) {
                $result[$j][$i] = $this->data[$i][$j];
            }
        }
        return new Matrix($result);
    }

    // --- DETERMINANTE (Recursivo) ---
    public function determinant(): float
    {
        if ($this->rows !== $this->cols) {
            throw new MatrixException("O determinante só pode ser calculado para matrizes quadradas.");
        }

        return $this->calculateDeterminant($this->data);
    }

    private function calculateDeterminant(array $matrix): float
    {
        $n = count($matrix);

        if ($n === 1) {
            return (float)$matrix[0][0];
        }

        if ($n === 2) {
            return (float)($matrix[0][0] * $matrix[1][1] - $matrix[0][1] * $matrix[1][0]);
        }

        $det = 0.0;
        for ($j = 0; $j < $n; $j++) {
            $subMatrix = [];
            for ($i = 1; $i < $n; $i++) {
                $row = [];
                for ($k = 0; $k < $n; $k++) {
                    if ($k !== $j) {
                        $row[] = $matrix[$i][$k];
                    }
                }
                $subMatrix[] = $row;
            }
            $sign = ($j % 2 === 0) ? 1 : -1;
            $det += $sign * $matrix[0][$j] * $this->calculateDeterminant($subMatrix);
        }

        return (float)$det;
    }

    // --- RESOLUÇÃO DE SISTEMAS LINEARES (Ax = B) - Eliminação de Gauss ---
    public function solveLinearSystem(array $b): array
    {
        if ($this->rows !== $this->cols) {
            throw new MatrixException("A matriz de coeficientes deve ser quadrada.");
        }

        if (count($b) !== $this->rows) {
            throw new MatrixException("O vetor de termos independentes deve ter o mesmo número de linhas da matriz.");
        }

        $n = $this->rows;
        $A = $this->data;
        $B = $b;

        // Eliminação Progressiva com Pivotamento Parcial
        for ($i = 0; $i < $n; $i++) {
            // Pivotamento
            $maxRow = $i;
            for ($k = $i + 1; $k < $n; $k++) {
                if (abs($A[$k][$i]) > abs($A[$maxRow][$i])) {
                    $maxRow = $k;
                }
            }

            // Trocar linhas se necessário
            $tempRow = $A[$i];
            $A[$i] = $A[$maxRow];
            $A[$maxRow] = $tempRow;

            $tempB = $B[$i];
            $B[$i] = $B[$maxRow];
            $B[$maxRow] = $tempB;

            if (abs($A[$i][$i]) < 1e-9) {
                throw new MatrixException("O sistema é impossível ou indeterminado (Matriz Singular).");
            }

            for ($k = $i + 1; $k < $n; $k++) {
                $factor = $A[$k][$i] / $A[$i][$i];
                $B[$k] -= $factor * $B[$i];
                for ($j = $i; $j < $n; $j++) {
                    $A[$k][$j] -= $factor * $A[$i][$j];
                }
            }
        }

        // Substituição Regressiva
        $x = array_fill(0, $n, 0.0);
        for ($i = $n - 1; $i >= 0; $i--) {
            $sum = 0.0;
            for ($j = $i + 1; $j < $n; $j++) {
                $sum += $A[$i][$j] * $x[$j];
            }
            $x[$i] = ($B[$i] - $sum) / $A[$i][$i];
        }

        return $x;
    }
}