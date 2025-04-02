<?php
session_start(); // Chama session_start() no começo do arquivo
include($_SERVER['DOCUMENT_ROOT'] . '/db.php');
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lubrificações</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5 p-4 border rounded bg-white">
        <!-- Botão de Voltar -->
        <a href="\manutencao\lubrificacao\index.php" class="btn btn-secondary">Voltar</a>

        <h2 class="text-center">Selecione um Formulário</h2>

        <label for="selecionar-form" class="form-label mt-3">Escolha uma opção:</label>
        <select id="selecionar-form" class="form-select" onchange="mostrarFormulario()">
            <option value="">Selecione...</option>
            <option value="formP100">P100</option>
            <option value="formP300">P300</option>
            <option value="formP310">P310</option>
            <option value="formP320">P320</option>
            <option value="formCaixa">Caixa do Contra Peso</option>
            <option value="formUnidadeHidraulica">Unidade Hídraulica</option>
            < </select>

                <!-- Formulário P100 -->
                <div id="formP100" class="formulario mt-4 p-3 border rounded bg-light" style="display: none;">
                    <h4>Formulário P100</h4>
                    <form method="POST" action="processo.php">
                        <input type="hidden" name="planta" value="P100">
                        <div class="mb-3">
                            <label class="form-label" for="PN01">PN-01:</label>
                            <input type="number" id="PN01" name="PN-001" class="form-control"
                                placeholder="Digite o horímetro do PN-01" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="PN02">PN-02:</label>
                            <input type="number" id="PN02" name="PN-002" class="form-control"
                                placeholder="Digite o horímetro do PN-02" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="MM01_P100">MM-01:</label>
                            <input type="number" id="MM01_P100" name="MM-001" class="form-control"
                                placeholder="Digite o horímetro do MM-01" step="0.01">
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </form>
                </div>

                <!-- Formulário P300 -->
                <div id="formP300" class="formulario mt-4 p-3 border rounded bg-light" style="display: none;">
                    <h4>Formulário P300</h4>
                    <form method="POST" action="processo.php">
                        <input type="hidden" name="planta" value="P300">
                        <div class="mb-3">
                            <label class="form-label" for="BR01">BR-01:</label>
                            <input type="number" id="BR01" name="BR-001" class="form-control"
                                placeholder="Digite o horímetro do BR-01" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="BR02">BR-02:</label>
                            <input type="number" id="BR02" name="BR-002" class="form-control"
                                placeholder="Digite o horímetro do BR-02" step="0.01">
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </form>
                </div>

                <!-- Formulário P310 -->
                <div id="formP310" class="formulario mt-4 p-3 border rounded bg-light" style="display: none;">
                    <h4>Formulário P310</h4>
                    <form method="POST" action="processo.php">
                        <input type="hidden" name="planta" value="P310">
                        <div class="mb-3">
                            <label class="form-label" for="MM01_P310">MM-01:</label>
                            <input type="number" id="MM01_P310" name="MM-001" class="form-control"
                                placeholder="Digite o horímetro do MM-01" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="MM02_P310">MM-02:</label>
                            <input type="number" id="MM02_P310" name="MM-002" class="form-control"
                                placeholder="Digite o horímetro do MM-02" step="0.01">
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </form>
                </div>



                <!-- Formulário Caixa de contra peso -->
                <div id="formCaixa" class="formulario mt-4 p-3 border rounded bg-light" style="display: none;">
                    <h4>Formulário Caixa de Contra Peso</h4>
                    <form method="POST" action="processo.php">
                        <input type="hidden" name="planta" value="P300">
                        <div class="mb-3">
                            <label class="form-label" for="CaixaDeContraPeso">Caixa de Contra Peso:</label>
                            <input type="number" id="Caixa_ContraPeso" name="CaixaContraPeso" class="form-control"
                                placeholder="Digite o horímetro da Caixa" step="0.01">
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </form>
                </div>
                <!-- Formulário Hidraulica -->
                <div id="formUnidadeHidraulica" class="formulario mt-4 p-3 border rounded bg-light"
                    style="display: none;">
                    <h4>Formulário Unidade Hídraulica</h4>
                    <form method="POST" action="processo.php">
                        <input type="hidden" name="planta" value="P300">
                        <div class="mb-3">
                            <label class="form-label" for="UnidadeHidraulica">Unidade Hidraulica:</label>
                            <input type="number" id="Unidade_Hidraulica" name="UnidadeHidraulica" class="form-control"
                                placeholder="Digite o horímetro da Unidade Hidraulica" step="0.01">
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </form>
                </div>

                <!-- Formulário P320 -->
                <div id="formP320" class="formulario mt-4 p-3 border rounded bg-light" style="display: none;">
                    <h4>Formulário P320</h4>
                    <form method="POST" action="processo.php">
                        <input type="hidden" name="planta" value="P320">
                        <div class="mb-3">
                            <label class="form-label" for="PN001_P320">Eixo Superior (PN-001):</label>
                            <input type="number" id="PN001_P320" name="PN-001" class="form-control"
                                placeholder="Digite o horímetro do PN-001" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="PN002_P320">Eixo Inferior (PN-001):</label>
                            <input type="number" id="PN002_P320" name="PN-002" class="form-control"
                                placeholder="Digite o horímetro do PN-001" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="UnidadeHidraulica_P320">Unidade Hidráulica:</label>
                            <input type="number" id="UnidadeHidraulica_P320" name="UnidadeHidraulica"
                                class="form-control" placeholder="Digite o horímetro da Unidade Hidráulica" step="0.01">
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </form>
                </div>


    </div>

    <!-- Toast do Bootstrap para exibir mensagens -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="toastMensagem" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive"
            aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php
                    if (isset($_SESSION['mensagem'])) {
                        echo $_SESSION['mensagem']['texto'];
                        echo "<script>document.getElementById('toastMensagem').classList.add('bg-{$_SESSION['mensagem']['tipo']}');</script>";
                        unset($_SESSION['mensagem']); // Remove a mensagem após exibição
                    }
                    ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Função JavaScript para exibir o formulário selecionado -->
    <script src="scripts.js"></script>

</body>

</html>