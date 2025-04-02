<?php
session_start();

include($_SERVER['DOCUMENT_ROOT'] . '/db.php');

// Carregar o Composer
require './pdf/vendor/autoload.php';

// Referenciar o namespace Dompdf
use Dompdf\Dompdf;

// Instanciar e usar a classe Dompdf
$dompdf = new Dompdf(['enable_remote' => true]);

// Consulta para a tabela Horímetros
$queryHorimetros = $conn->query("SELECT * FROM `manutencao_lubrificacaohorimetros` ORDER BY `id` DESC");
$horimetros = [];
while ($row = $queryHorimetros->fetch_assoc()) {
    $horimetros[] = $row;
}
$dados = '
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .logo {
        display: block;
        margin: 0 auto;
        width: 150px; /* ajuste conforme necessário */
    }
    .header-text {
        text-align: center;
        margin: 20px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
    }
    thead th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tbody tr:hover {
        background-color: #e9e9e9;
    }
</style>

<div style="text-align: center;">
    <img src="https://portal.morroverde.mv/assets/img/logo3.png" class="logo">
</div>
<div class="header-text">
    <h2>Relatório de Manutenção Mecânica</h2>
    <p>Este relatório apresenta as lubrificações realizadas nos equipamentos, evidenciando as manutenções mecânicas efetuadas.</p>
</div>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Planta</th>
            <th>Equipamento</th>
            <th>Horímetro</th>
            <th>Data</th>
        </tr>
    </thead>';

foreach ($horimetros as $horimetro) {
    $dados .= '
    <tr>
        <td>' . htmlspecialchars($horimetro['id']) . '</td>
        <td>' . htmlspecialchars($horimetro['planta']) . '</td>
        <td>' . htmlspecialchars($horimetro['equipamento']) . '</td>
        <td>' . htmlspecialchars($horimetro['horimetro']) . '</td>
        <td>' . htmlspecialchars($horimetro['dtLubrificacao']) . '</td>
    </tr>';
}

$dados .= '</table>';
// Instanciar o método loadHtml e enviar o conteúdo do PDF
$dompdf->loadHtml($dados);

// Configurar as opções para garantir a remoção das margens
$options = $dompdf->getOptions();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$options->set('defaultPaperSize', 'A4');
$dompdf->setOptions($options);

// Definir o papel e as margens como 0
$dompdf->setPaper('A4', 'portrait'); // Configurar o papel A4 no formato retrato
$dompdf->set_option('margin-top', 0); // Remover a margem superior
$dompdf->set_option('margin-right', 0); // Remover a margem direita
$dompdf->set_option('margin-bottom', 0); // Remover a margem inferior
$dompdf->set_option('margin-left', 0); // Remover a margem esquerda

// Renderizar o HTML como PDF
$dompdf->render();

$dompdf->stream("relatorio.pdf", ["Attachment" => true]);

// Gerar o PDF e salvar em uma pasta local
$output = $dompdf->output();