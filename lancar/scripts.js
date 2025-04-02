function mostrarFormulario() {
    document.querySelectorAll('.formulario').forEach(form => {
        form.style.display = 'none';
    });

    let opcaoSelecionada = document.getElementById("selecionar-form").value;
    if (opcaoSelecionada) {
        document.getElementById(opcaoSelecionada).style.display = 'block';
    }
}

// Inicializa o Toast automaticamente ao carregar a página
document.addEventListener("DOMContentLoaded", function () {
    var toastEl = document.getElementById('toastMensagem');
    if (toastEl.innerText.trim() !== "") {
        var toast = new bootstrap.Toast(toastEl);
        toast.show();
    }
});

//Envio de informações pelo botão 
function validarFormulario(event) {
    event.preventDefault(); // Impede o envio padrão do formulário

    // Obtém o formulário ativo
    let formularioVisivel = document.querySelector('.formulario[style="display: block;"]');
    if (!formularioVisivel) {
        exibirToast("Por favor, selecione um formulário!", "danger");
        return;
    }

    let inputs = formularioVisivel.querySelectorAll("input[type='text'], input[type='number']");
    let temValorPreenchido = false;
    let formData = new FormData();

    inputs.forEach(input => {
        if (input.value.trim() !== "") {
            formData.append(input.name, input.value);
            temValorPreenchido = true;
        }
    });

    if (!temValorPreenchido) {
        exibirToast("Preencha pelo menos um campo antes de enviar!", "danger");
        return;
    }

    // Adiciona os campos ocultos ao FormData (como 'planta')
    formularioVisivel.querySelectorAll("input[type='hidden']").forEach(hiddenInput => {
        formData.append(hiddenInput.name, hiddenInput.value);
    });

    // Envia os dados via POST
    fetch(formularioVisivel.querySelector("form").action, {
        method: "POST",
        body: formData
    }).then(response => response.text()).then(result => {
        exibirToast("Formulário enviado com sucesso!", "success");
        console.log("Resposta do servidor:", result);
    }).catch(error => {
        exibirToast("Erro ao enviar o formulário!", "danger");
        console.error("Erro:", error);
    });
}


function exibirToast(mensagem, tipo) {
    let toastEl = document.getElementById('toastMensagem');
    let toastBody = toastEl.querySelector('.toast-body');

    toastEl.classList.remove('bg-danger', 'bg-success');
    toastEl.classList.add(`bg-${tipo}`);

    toastBody.innerHTML = mensagem;
    let toast = new bootstrap.Toast(toastEl);
    toast.show();
}