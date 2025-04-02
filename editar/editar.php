<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');

// Verifica se o ID foi passado na URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta para pegar os dados do registro específico
    $query = "SELECT * FROM manutencao_lubrificacaohorimetros WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        $_SESSION['mensagem'] = ["tipo" => "danger", "texto" => "Registro não encontrado."];
        header("Location: editarLubrificacoes.php");
        exit();
    }

    // Fecha a consulta
    $stmt->close();
} else {
    $_SESSION['mensagem'] = ["tipo" => "danger", "texto" => "Erro: Nenhum ID fornecido."];
    header("Location: editarLubrificacoes.php");
    exit();
}

// Se o formulário for submetido, realiza a atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planta = $_POST['planta'];
    $equipamento = $_POST['equipamento'];
    $horimetro = $_POST['horimetro'];

    // Atualiza os dados no banco de dados
    $updateQuery = "UPDATE manutencao_lubrificacaohorimetros SET planta = ?, equipamento = ?, horimetro = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssi", $planta, $equipamento, $horimetro, $id);
    
    if ($updateStmt->execute()) {
        $_SESSION['mensagem'] = ["tipo" => "success", "texto" => "Dados atualizados com sucesso!"];
        header("Location: editarLubrificacoes.php");
        exit();
    } else {
        $_SESSION['mensagem'] = ["tipo" => "danger", "texto" => "Erro ao atualizar os dados!"];
    }

    $updateStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Registro de Lubrificação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <!-- Botão Voltar -->
        <a href="editarLubrificacoes.php" class="btn btn-secondary mb-3">Voltar</a>

        <div class="card">
            <div class="card-header">
                <h3>Editar Registro de Lubrificação</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="editar.php?id=<?php echo $row['id']; ?>">
                    <div class="mb-3">
                        <label for="planta" class="form-label">Planta</label>
                        <input type="text" class="form-control" id="planta" name="planta"
                            value="<?php echo $row['planta']; ?>" required readonly>
                    </div>
                    <div class="mb-3">
                        <label for="equipamento" class="form-label">Equipamento</label>
                        <input type="text" class="form-control" id="equipamento" name="equipamento"
                            value="<?php echo $row['equipamento']; ?>" required readonly>
                    </div>
                    <div class="mb-3">
                        <label for="horimetro" class="form-label">Horímetro</label>
                        <input type="text" class="form-control" id="horimetro" name="horimetro"
                            value="<?php echo $row['horimetro']; ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Atualizar</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>