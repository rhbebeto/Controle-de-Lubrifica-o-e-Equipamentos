<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');

// Consulta para obter os registros de lubrificação com a data
$query = "SELECT * FROM manutencao_lubrificacaohorimetros ORDER BY id DESC";
$result = $conn->query($query);

// Início do HTML
echo '
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros de Lubrificação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between">
        <a href="\manutencao\lubrificacao\index.php" class="btn btn-secondary mb-1">Voltar</a>
        <a href="\manutencao\lubrificacao\editar\emitirPDF.php" class="btn btn-success btn-lg mb-1">Emitir Histórico</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Registros de Lubrificação</h3>
        </div>
        <div class="card-body">
            <!-- Tabela de Registros -->
            <table id="lubrificacaoTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Planta</th>
                        <th>Equipamento</th>
                        <th>Horímetro</th>
                        <th>Data</th>
                         
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>';

                // Verifique se há registros
                if ($result->num_rows > 0) {
                    // Exibe os registros
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id']}</td>
                                <td>{$row['planta']}</td>
                                <td>{$row['equipamento']}</td>
                                <td>{$row['horimetro']}</td>
                                <td>" . date('d/m/Y ', strtotime($row['dtLubrificacao'])) . "</td>
                                
                                <td>
                                    <a href='editar.php?id={$row['id']}' class='btn btn-warning btn-sm'>Editar</a>
                                    <a href='excluir.php?id={$row['id']}' class='btn btn-danger btn-sm'>Excluir</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Não há registros de lubrificação.</td></tr>";
                }

                echo '</tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script>
 $(document).ready(function() {
    $("#lubrificacaoTable").DataTable({
        "order": [[0, "desc"]], 
        "pagingType": "simple_numbers",
        "pageLength": 20, // Define o número fixo de registros por página
        "lengthChange": false, // Remove a opção de alterar o número de registros por página
        "language": {
            "url": "//cdn.datatables.net/plug-ins/2.2.2/i18n/pt-BR.json"
        }
    });
});
</script>

</body>
</html>';

$conn->close();
?>