<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');

// Buscar os últimos horímetros registrados, incluindo a planta, 
// garantindo que a linha retornada seja aquela com o maior id para cada (equipamento, planta)
// que possua hora_final preenchido (não nulo, não vazio e diferente de '0')
$query_horimetros = "
    SELECT p.equipamento, p.planta, p.hora_final
    FROM pcp_horimetros p
    WHERE p.hora_final IS NOT NULL 
      AND p.hora_final != '' 
      AND p.hora_final != '0'
      AND p.id = (
            SELECT MAX(id)
            FROM pcp_horimetros
            WHERE equipamento = p.equipamento
              AND planta = p.planta
              AND hora_final IS NOT NULL 
              AND hora_final != '' 
              AND hora_final != '0'
      )
    ORDER BY p.planta, p.equipamento
";

$result_horimetros = $conn->query($query_horimetros);
$horimetros = [];

if ($result_horimetros && $result_horimetros->num_rows > 0) {
    while ($row = $result_horimetros->fetch_assoc()) {
        $chave = $row['equipamento'] . '_' . $row['planta'];
        $horimetros[$chave] = [
            'equipamento' => $row['equipamento'],
            'hora_final' => floatval(str_replace(',', '.', $row['hora_final'])),
            'planta' => $row['planta']
        ];
    }
}

// Buscar os últimos horímetros da última lubrificação, incluindo a planta, 
// garantindo que a linha retornada seja aquela com o maior id para cada (equipamento, planta)
$query_lubrificacoes = "
    SELECT m.equipamento, m.planta, m.horimetro
    FROM manutencao_lubrificacaohorimetros m
    INNER JOIN (
        SELECT equipamento, planta, MAX(id) AS max_id
        FROM manutencao_lubrificacaohorimetros
        GROUP BY equipamento, planta
    ) AS sub
    ON m.equipamento = sub.equipamento 
       AND m.planta = sub.planta 
       AND m.id = sub.max_id
";

$result_lubrificacoes = $conn->query($query_lubrificacoes);
$lubrificacoes = [];

if ($result_lubrificacoes && $result_lubrificacoes->num_rows > 0) {
    while ($row = $result_lubrificacoes->fetch_assoc()) {
        $chave = $row['equipamento'] . '_' . $row['planta'];
        $lubrificacoes[$chave] = [
            'equipamento' => $row['equipamento'],
            'horimetro' => floatval($row['horimetro']),
            'planta' => $row['planta']
        ];

        // Adicionar ao array de horímetros caso o equipamento não esteja presente
        if (!isset($horimetros[$chave])) {
            $horimetros[$chave] = [
                'equipamento' => $row['equipamento'],
                'hora_final' => 0, // Adicionar um valor padrão
                'planta' => $row['planta']
            ];
        }
    }
}

// Ajustar os horímetros atuais de CaixaContraPeso e UnidadeHidraulica (caso haja necessidade)
if (isset($horimetros['BR-001_P300'])) {
    $horimetros['CaixaContraPeso_P300'] = [
        'equipamento' => 'CaixaContraPeso',
        'hora_final' => $horimetros['BR-001_P300']['hora_final'],
        'planta' => 'P300'
    ];
}
if (isset($horimetros['BR-002_P300'])) {
    $horimetros['UnidadeHidraulica_P300'] = [
        'equipamento' => 'UnidadeHidraulica',
        'hora_final' => $horimetros['BR-002_P300']['hora_final'],
        'planta' => 'P300'
    ];
}

// Remover registros indesejados, se necessário
unset($horimetros['MM-003_P310']);
unset($horimetros['MM-004_P310']);

$conn->close();

// Passar os dados para o JavaScript
$horimetros_json = json_encode($horimetros, JSON_NUMERIC_CHECK);
$lubrificacoes_json = json_encode($lubrificacoes, JSON_NUMERIC_CHECK);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Lubrificações</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <a href="\manutencao\lubrificacao\index.php" class="btn btn-secondary">Voltar</a>
        <button id="calcularLubrificacao" class="btn btn-primary">Calcular Lubrificações</button>

        <table id="tabelaLubrificacao" class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Planta</th>
                    <th>Equipamento</th>
                    <th>Horímetro Atual</th>
                    <th>Horímetro Última Lubrificação</th>
                    <th>Horas Passadas</th>
                    <th>Limite</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="resultadoLubrificacao">
                <!-- A tabela será preenchida pelo JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Passar os dados do PHP para o JavaScript -->
    <script>
    var horimetros = <?php echo $horimetros_json; ?>;
    var lubrificacoes = <?php echo $lubrificacoes_json; ?>;
    console.log("Horímetros:", horimetros);
    console.log("Lubrificações:", lubrificacoes);
    </script>
    <!-- Arquivo JavaScript externo -->
    <script src="scripts.js"></script>
</body>

</html>