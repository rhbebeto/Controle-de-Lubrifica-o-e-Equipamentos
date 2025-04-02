<?php

include($_SERVER['DOCUMENT_ROOT'] . '/db.php');

session_start();
if (!isset($_SESSION['logado'])) {
    header("location: /");
}


$logado = $_SESSION['logado'];

$sql = "SELECT * FROM usuarios_perm WHERE email = '$logado' AND perm in ('manutencao_lubrificacao','manutencao_admin','admin')";
$result = mysqli_query($conn, $sql);
$rows = mysqli_num_rows($result);

if ($rows < '1') {
    header("location: /");
}

?>
<html>

<head>
    <title>Morro Verde - Manutenção</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <script src="/assets/js/bootstrap.bundle.min.js"> </script>
    <link rel="icon" href="/assets/img/icon.png" sizes="192x192" />
    <link href='/assets/css/poppins.css' rel='stylesheet'>
    <link rel="stylesheet" href="/assets/css/bootstrap-icons/font/bootstrap-icons.min.css" />
    <style>
    body {
        font-family: 'Poppins';
    }

    #background-video {
        width: 100vw;
        height: 100vh;
        object-fit: cover;
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        z-index: -1;
    }

    p {
        font-size: 14px;
    }
    </style>
</head>

<body class="p-2 m-0 border-0 m-0 border-0" style="text-align: center;">

    <video id="background-video" class="K8MSra" role="presentation" crossorigin="anonymous" playsinline=""
        preload="auto" muted="" loop="" tabindex="-1" autoplay=""
        src="https://video.wixstatic.com/video/95ed09_1a293a765eef4b1dbce92586dfc90294/1080p/mp4/file.mp4"
        style="height: 100%; width: 100%; object-fit: cover; object-position: center center; opacity: 1;"></video>
    <br>
    <div
        style="background-color: rgba(255, 255, 255, 0.5);border-radius: 20px;max-width: 400px;padding: 20px;margin: 0 auto;">
        <h1 style="font-size: 20px;">Selecione o Módulo:</h1>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/modulo.php"
                        style="color: black;text-decoration: none;">Modulos</a></li>
                <li class="breadcrumb-item" aria-current="page">Manunteção</li>
                <li class="breadcrumb-item active" aria-current="page">Lubrificação</li>
            </ol>
        </nav>
        <div class="row">
            <div class="col-4 col-md-4 text-center mb-4"><a
                    href="/manutencao/lubrificacao/lancar/lancarHorimetros.php"
                    style="color: black;text-decoration: none;display: inline-block;"><img src="/assets/img/lub2.png"
                        style="width: 50px;vertical-align: middle;">
                    <p style="display: block;">Lançar Lubrificação</p>
                </a></div>

            <div class="col-4 col-md-4 text-center mb-4"><a
                    href="/manutencao/lubrificacao/consultar/consultarHorimetros.php"
                    style="color: black;text-decoration: none;display: inline-block;"><img src="/assets/img/lub3.png"
                        style="width: 50px;vertical-align: middle;">
                    <p style="display: block;">Consultar Horímetro</p>
                </a>
            </div>

            <div class="col-4 col-md-4 text-center mb-4"><a
                    href="/manutencao/lubrificacao/editar/editarLubrificacoes.php"
                    style="color: black;text-decoration: none;display: inline-block;"><img src="/assets/img/lub1.png"
                        style="width: 50px;vertical-align: middle;border-radius: 50px;">
                    <p style="display: block;">Editar Lubrificações</p>
                </a>
            </div>

            <a href="/manutencao" style="list-style: none;text-decoration: none;color:black;"><i
                    class="bi bi-arrow-left-circle-fill" style="font-size: 38px;"></i></a>
        </div>
    </div>
    <br>
</body>

</html>