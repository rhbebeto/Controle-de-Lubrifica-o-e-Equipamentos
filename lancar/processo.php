<?php
session_start(); // Inicia a sessão para exibir mensagens

include($_SERVER['DOCUMENT_ROOT'] . '/db.php');

// Captura a planta selecionada
$planta = trim($_POST['planta'] ?? '');

// Definição dos equipamentos por planta
$equipamentos_por_planta = [
    "P100" => ["PN-001", "PN-002", "MM-001"],
    "P300" => ["BR-001", "BR-002", "CaixaContraPeso", "UnidadeHidraulica"], // Adicionados novos equipamentos
    "P310" => ["MM-001", "MM-002"],
    "P320" => ["PN-001", "PN-002", "UnidadeHidraulica"]

];

// Verifica se a planta é válida
if (!isset($equipamentos_por_planta[$planta])) {
    $_SESSION['mensagem'] = ["tipo" => "danger", "texto" => "Erro: Planta inválida."];
    header("Location: \manutencao\gerenciar\lubrificacao\lancar\lancarHorimetros.php");
    exit();
}

// Obtém os equipamentos esperados apenas para a planta do formulário enviado
$equipamentos_necessarios = $equipamentos_por_planta[$planta];

$temPeloMenosUmPreenchido = false;
$dados = [];

foreach ($equipamentos_necessarios as $campo) {
    $valor = trim($_POST[$campo] ?? '');

    if ($valor !== '') {
        $temPeloMenosUmPreenchido = true;
        $dados[$campo] = $valor;
    }
}

// Se nenhum campo foi preenchido, exibe erro
if (!$temPeloMenosUmPreenchido) {
    $_SESSION['mensagem'] = [
        "tipo" => "warning",
        "texto" => "Erro: Pelo menos um campo da planta $planta deve ser preenchido."
    ];
    header("Location: \manutencao\gerenciar\lubrificacao\lancar\lancarHorimetros.php");
    exit();
}

// Insere os dados no banco de dados apenas para os campos preenchidos
$stmt = $conn->prepare("INSERT INTO manutencao_lubrificacaohorimetros (planta, equipamento, horimetro, dtLubrificacao) VALUES (?, ?, ?, NOW())");
if ($stmt) {
    foreach ($dados as $equipamento => $horimetro) {
        $stmt->bind_param("sss", $planta, $equipamento, $horimetro);
        $stmt->execute();
    }
    $stmt->close();
}

// Mensagem de sucesso
$_SESSION['mensagem'] = ["tipo" => "success", "texto" => "Dados inseridos com sucesso!"];
header("Location: \manutencao\lubrificacao\lancar\lancarHorimetros.php");
exit();
?>