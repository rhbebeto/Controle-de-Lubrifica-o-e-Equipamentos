$(document).ready(function () {

    // Função para garantir que os valores sejam numéricos, tratando valores vazios e substituindo vírgula por ponto
    function toNumero(valor) {
        if (typeof valor === 'string') {
            valor = valor.replace(',', '.');
        }
        return isNaN(parseFloat(valor)) ? 0 : parseFloat(valor);
    }

    // Função para calcular a diferença de horas (com 2 casas decimais)
    function calcularHorasPassadas(horaAtual, horaUltimaLubrificacao) {
        return (horaAtual - horaUltimaLubrificacao).toFixed(2);
    }

    // Função para determinar o status com base no limite
    function calcularStatus(horasPassadas, planta, equipamento) {
        var plantaUpper = planta.trim().toUpperCase();
        // Obtém o limite configurado para este equipamento na planta
        var limite = (limites[plantaUpper] && limites[plantaUpper][equipamento]) ? limites[plantaUpper][equipamento] : null;
        if (limite === null) {
            console.warn(`Limite não encontrado para ${plantaUpper} - ${equipamento}`);
            return 'Sem Dados ⚠️';
        }
        return (horasPassadas >= limite) ? 'Pendente⭕' : 'Dentro do Prazo✅';
    }

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
        "P300": {
            "BR-001": 80,
            "BR-002": 80,
            "CaixaContraPeso": 300,
            "UnidadeHidraulica": 2000
        },
        "P310": {
            "MM-001": 90,
            "MM-002": 90
        },
        "P320": {
            "PN-001": 1500,
            "PN-002": 1500,
            "UnidadeHidraulica": 2000
        }
    };

    // Função para preencher a tabela com os dados calculados
    function preencherTabela() {
        var resultadoLubrificacao = $('#resultadoLubrificacao');
        resultadoLubrificacao.empty();

        // Percorre os dados de horímetros
        for (var chave in horimetros) {
            var dados = horimetros[chave];
            var equipamento = dados.equipamento;
            var planta = dados.planta;
            var horaAtual = toNumero(dados.hora_final);

            // Se for P320 e o equipamento for VS-001, não exibe essa linha (oculta o valor de VSI)
            if (planta.trim().toUpperCase() === "P320" && equipamento === "VS-001") {
                continue;
            }

            // Se a planta for P320, usamos o horímetro de VS-001 para todos os equipamentos
            if (planta.trim().toUpperCase() === "P320") {
                var keyVSI = "VS-001_P320";
                if (horimetros[keyVSI]) {
                    horaAtual = toNumero(horimetros[keyVSI].hora_final);
                }
            }

            // Cria a chave composta para buscar lubrificações
            var lubKey = equipamento + '_' + planta;
            var horaUltimaLubrificacao = (lubrificacoes[lubKey] && lubrificacoes[lubKey].horimetro) ?
                toNumero(lubrificacoes[lubKey].horimetro) : 0;

            var horasPassadas = calcularHorasPassadas(horaAtual, horaUltimaLubrificacao);
            var status = calcularStatus(horasPassadas, planta, equipamento);

            // Obter o limite para exibição (ou 'N/A' se não existir)
            var plantaUpper = planta.trim().toUpperCase();
            var limiteEquipamento = (limites.hasOwnProperty(plantaUpper) && limites[plantaUpper].hasOwnProperty(equipamento)) ?
                limites[plantaUpper][equipamento] : 'N/A';

            var linha = `
                <tr>
                    <td>${planta}</td>
                    <td>${equipamento}</td>
                    <td>${horaAtual.toFixed(2)}</td>
                    <td>${horaUltimaLubrificacao.toFixed(2)}</td>
                    <td>${horasPassadas}</td>
                    <td>${limiteEquipamento} hrs</td>
                    <td>${status}</td>
                </tr>
            `;
            resultadoLubrificacao.append(linha);
        }
    }


    function inicializarDataTable() {
        $('#tabelaLubrificacao').DataTable({
            "paging": true,
            "searching": false,      // Desativa a pesquisa
            "ordering": true,
            "info": true,
            "pageLength": 20, // Define o número fixo de registros por página
            "lengthChange": false,
            "destroy": true,         // Para reinicializar sem duplicar
            "language": {
                "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/Portuguese-Brasil.json"
            }
        });
    }
    // Preenche a tabela e inicializa o DataTable
    preencherTabela();
    inicializarDataTable();

    // Atualiza a tabela quando o botão é clicado
    $('#calcularLubrificacao').click(function () {
        preencherTabela();
        inicializarDataTable();
    });
});
