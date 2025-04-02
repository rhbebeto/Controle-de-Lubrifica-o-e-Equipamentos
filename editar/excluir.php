<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');

// Verifica se o ID foi passado na URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prepara a consulta SQL para excluir o registro
    $stmt = $conn->prepare("DELETE FROM manutencao_lubrificacaohorimetros WHERE id = ?");
    $stmt->bind_param("i", $id);

    // Executa a consulta e verifica se foi bem-sucedido
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = ["tipo" => "success", "texto" => "Registro excluído com sucesso!"];
    } else {
        $_SESSION['mensagem'] = ["tipo" => "danger", "texto" => "Erro ao excluir o registro!"];
    }

    // Redireciona de volta para a página de registros
    header("Location: editarLubrificacoes.php");
    exit();
} else {
    $_SESSION['mensagem'] = ["tipo" => "danger", "texto" => "Erro: Nenhum ID fornecido."];
    header("Location: editarLubrificacoes.php");
    exit();
}

$conn->close();
?>