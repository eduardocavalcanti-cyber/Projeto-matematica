<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Álgebra Linear - PHP 8.4</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <h1 class="mb-4 text-primary text-center">Calculadora de Álgebra Linear</h1>

        <div class="card shadow-sm p-4 mb-4">
            <form action="processar.php" method="POST">
                <div class="mb-3">
                    <label for="operacao" class="form-label fw-bold">Selecione a Operação:</label>
                    <select name="operacao" id="operacao" class="form-select" required>
                        <option value="somar">Soma de Matrizes (A + B)</option>
                        <option value="multiplicar">Multiplicação de Matrizes (A * B)</option>
                        <option value="transpor">Transposição da Matriz A</option>
                        <option value="determinante">Determinante da Matriz A</option>
                        <option value="sistema">Sistema Linear (Ax = B)</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="matrizA" class="form-label fw-bold">Matriz A (Espaço separa colunas, Enter separa linhas):</label>
                        <textarea name="matrizA" id="matrizA" class="form-control" rows="5" placeholder="1 2&#10;3 4" required></textarea>
                    </div>

                    <div class="col-md-6 mb-3" id="containerMatrizB">
                        <label for="matrizB" id="labelMatrizB" class="form-label fw-bold">Matriz B / Vetor B:</label>
                        <textarea name="matrizB" id="matrizB" class="form-control" rows="5" placeholder="5 6&#10;7 8"></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 fw-bold">Calcular</button>
            </form>
        </div>
    </div>

    <script>
        const selectOperacao = document.getElementById('operacao');
        const containerMatrizB = document.getElementById('containerMatrizB');
        const labelMatrizB = document.getElementById('labelMatrizB');
        const textareaB = document.getElementById('matrizB');

        function atualizarInterface() {
            const op = selectOperacao.value;
            
            if (op === 'transpor' || op === 'determinante') {
                containerMatrizB.style.display = 'none';
                textareaB.removeAttribute('required');
            } else if (op === 'sistema') {
                containerMatrizB.style.display = 'block';
                labelMatrizB.textContent = 'Vetor B (Termos Independentes):';
                textareaB.placeholder = "5\n10";
                textareaB.setAttribute('required', 'required');
            } else {
                containerMatrizB.style.display = 'block';
                labelMatrizB.textContent = 'Matriz B:';
                textareaB.placeholder = "5 6\n7 8";
                textareaB.setAttribute('required', 'required');
            }
        }

        selectOperacao.addEventListener('change', atualizarInterface);
        atualizarInterface();
    </script>
</body>
</html>