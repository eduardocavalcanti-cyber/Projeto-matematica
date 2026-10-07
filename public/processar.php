<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Math\Matrix;
use App\Exceptions\MatrixException;

function parseMatrixText(string $text): array
{
    $lines = explode("\n", trim($text));
    $matrix = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') continue;
        $row = preg_split('/\s+/', $line);
        $matrix[] = array_map('floatval', $row);
    }
    return $matrix;
}

$operacao = $_POST['operacao'] ?? '';
$textA = $_POST['matrizA'] ?? '';
$textB = $_POST['matrizB'] ?? '';

$erro = null;
$resultado = null;

try {
    $dataA = parseMatrixText($textA);
    $matrizA = new Matrix($dataA);

    switch ($operacao) {
        case 'somar':
            $dataB = parseMatrixText($textB);
            $matrizB = new Matrix($dataB);
            $resultado = $matrizA->add($matrizB)->getData();
            break;

        case 'multiplicar':
            $dataB = parseMatrixText($textB);
            $matrizB = new Matrix($dataB);
            $resultado = $matrizA->multiply($matrizB)->getData();
            break;

        case 'transpor':
            $resultado = $matrizA->transpose()->getData();
            break;

        case 'determinante':
            $resultado = "Determinante: " . $matrizA->determinant();
            break;

        case 'sistema':
            $rawB = parseMatrixText($textB);
            
            // Converte tanto entrada em linha (5 10) quanto em coluna (5 \n 10) em array simples
            $flatB = [];
            foreach ($rawB as $row) {
                foreach ($row as $val) {
                    $flatB[] = $val;
                }
            }

            $x = $matrizA->solveLinearSystem($flatB);
            $resultado = [];
            foreach ($x as $index => $val) {
                $resultado["x" . ($index + 1)] = $val;
            }
            break;

        default:
            $erro = "Operação inválida selecionada.";
    }
} catch (MatrixException $e) {
    $erro = $e->getMessage();
} catch (Throwable $e) {
    $erro = "Erro no processamento dos dados: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado - Álgebra Linear</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <h2 class="mb-4">Resultado da Operação</h2>

        <?php if ($erro): ?>
            <div class="alert alert-danger shadow-sm">
                <strong>Erro:</strong> <?= htmlspecialchars($erro) ?>
            </div>
        <?php else: ?>
            <div class="card p-4 shadow-sm">
                <h5 class="mb-3">Saída:</h5>
                <pre class="bg-dark text-light p-3 rounded mb-0"><?= htmlspecialchars(print_r($resultado, true)) ?></pre>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn btn-secondary mt-3">Voltar</a>
    </div>
</body>
</html>