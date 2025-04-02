<?php
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

// Ajustar os horímetros atuais de CaixaContraPeso e UnidadeHidraulica
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

// Remover plantas vazias de MM-003 e MM-004 da P310
unset($horimetros['MM-003_P310']);
unset($horimetros['MM-004_P310']);

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Lubrificações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <a href="\manutencao\lubrificacao\index.php" class="btn btn-secondary">Voltar</a>
        <button id="calcularLubrificacao" class="btn btn-primary">Calcular Lubrificações</button>

        <table class="table table-bordered mt-3">
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
                <!-- A tabela será preenchida aqui pelo JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Inclusão do jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    // Mapeamento dos limites para cada equipamento por planta
    var limites = {
        "P100": {
            "MV-001": 80,
            "PN-001": 80,
            "PN-002": 80,
            "BR-001": 80,
            "BR-002": 80,
            "MM-001": 84
        },
        "P300": { // Certifique-se de que o nome está correto!
            "BR-001": 80,
            "BR-002": 80,
            "CaixaContraPeso": 300,
            "UnidadeHidraulica": 2000
        },
        "P310": { // Certifique-se de que o nome está correto!
            "MM-001": 90,
            "MM-002": 90
        },
        "P320": { // Certifique-se de que o nome está correto!
            "VS-001": 90
        }
    };

    // Passando os dados do PHP para o JavaScript
    var horimetros = <?php echo json_encode($horimetros); ?>;
    var lubrificacoes = <?php echo json_encode($lubrificacoes); ?>;

    $(document).ready(function() {
        // Função para calcular as horas passadas
        function calcularHorasPassadas(horaAtual, horaUltimaLubrificacao) {
            return (horaAtual - horaUltimaLubrificacao).toFixed(2);
        }

        // Função para determinar o status com base nas horas passadas e limites específicos
        function calcularStatus(horasPassadas, planta, equipamento) {
            var plantaUpper = planta.trim().toUpperCase();
            var limite = (limites[plantaUpper] && limites[plantaUpper][equipamento]) ?
                limites[plantaUpper][equipamento] :
                null; // Evita atribuir 80 diretamente

            if (limite === null) {
                console.warn(
                    `Limite não encontrado para ${plantaUpper} - ${equipamento}, verificando fallback.`);
                return 'Sem Dados ⚠️'; // Caso não tenha um valor definido, indica ausência de dados
            }

            return (horasPassadas >= limite) ? 'Pendente⭕' : 'Dentro do Prazo✅';
        }

        function preencherTabela() {
            var resultadoLubrificacao = $('#resultadoLubrificacao');
            resultadoLubrificacao.empty();

            for (var chave in horimetros) {
                var dados = horimetros[chave];
                var equipamento = dados.equipamento;
                var planta = dados.planta;
                var horaAtual = dados.hora_final;
                var lubKey = equipamento + '_' + planta;
                var horaUltimaLubrificacao = (lubrificacoes[lubKey] && lubrificacoes[lubKey].horimetro) ?
                    lubrificacoes[lubKey].horimetro : 0;

                // Ajustar horaAtual para CaixaContraPeso e UnidadeHidraulica
                if (equipamento === 'CaixaContraPeso') {
                    horaAtual = horimetros['BR-001_P300'] ? horimetros['BR-001_P300'].hora_final : horaAtual;
                } else if (equipamento === 'UnidadeHidraulica') {
                    horaAtual = horimetros['BR-002_P300'] ? horimetros['BR-002_P300'].hora_final : horaAtual;
                }

                var horasPassadas = calcularHorasPassadas(horaAtual, horaUltimaLubrificacao);
                var status = calcularStatus(horasPassadas, planta, equipamento);

                // Garantindo que a planta esteja no objeto limites
                var plantaUpper = planta.trim().toUpperCase(); // Removendo espaços extras
                var limiteEquipamento = (limites.hasOwnProperty(plantaUpper) && limites[plantaUpper]
                        .hasOwnProperty(equipamento)) ?
                    limites[plantaUpper][equipamento] :
                    'N/A'; // Em vez de 80, colocar um valor indicando que não há limite definido

                var linha = `
            <tr>
                <td>${planta}</td>
                <td>${equipamento}</td>
                <td>${horaAtual}</td>
                <td>${horaUltimaLubrificacao}</td>
                <td>${horasPassadas}</td>
                <td>${limiteEquipamento} hrs</td>
                <td>${status}</td>
            </tr>
        `;
                resultadoLubrificacao.append(linha);
            }
        }

        preencherTabela();
        $('#calcularLubrificacao').click(function() {
            preencherTabela();
        });
    });
    </script>
</body>

</html>