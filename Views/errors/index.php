<?php
// Certifica que nenhum output foi enviado antes
if (!headers_sent()) {
    header('HTTP/1.1 404 Not Found');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página não encontrada</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --accent-color: #f8f9fc;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-color: var(--accent-color);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }

        .error-page {
            position: relative;
        }

        .error-number {
            font-size: 12rem;
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(45deg, var(--primary-color), #7c8fd8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 2px 2px 10px rgba(78, 115, 223, 0.1);
        }

        .error-image {
            max-width: 500px;
            width: 100%;
            height: auto;
            margin: 2rem 0;
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0% {
                transform: translate(0, 0px);
            }

            50% {
                transform: translate(0, 15px);
            }

            100% {
                transform: translate(0, -0px);
            }
        }

        .particle {
            position: absolute;
            width: 10px;
            height: 10px;
            background: var(--primary-color);
            border-radius: 50%;
            opacity: 0.3;
        }

        .particle:nth-child(1) {
            top: 20%;
            left: 20%;
            animation: float 6s infinite;
        }

        .particle:nth-child(2) {
            top: 60%;
            left: 80%;
            animation: float 8s infinite;
        }

        .particle:nth-child(3) {
            top: 40%;
            left: 40%;
            animation: float 10s infinite;
        }

        .particle:nth-child(4) {
            top: 80%;
            left: 60%;
            animation: float 5s infinite;
        }

        .particle:nth-child(5) {
            top: 30%;
            left: 70%;
            animation: float 7s infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(20px, 20px);
            }
        }

        .btn-custom {
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
        }

        .search-box {
            max-width: 500px;
            margin: 0 auto;
        }

        .error-details {
            color: var(--secondary-color);
        }

        .timestamp {
            font-size: 0.9rem;
            color: var(--secondary-color);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="error-page text-center py-5">
            <!-- Partículas decorativas -->
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>

            <!-- Conteúdo principal -->
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <h1 class="error-number mb-4">404</h1>
                    <img src="https://illustrations.popsy.co/purple/crash.svg" alt="Error 404" class="error-image floating">
                    <h2 class="display-6 mb-3">Oops! Página não encontrada</h2>
                    <p class="lead error-details mb-4">
                        A página que você está procurando pode ter sido removida, renomeada ou está temporariamente indisponível.
                    </p>

                    <!-- Barra de pesquisa -->
                    <div class="search-box mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg" placeholder="Buscar no site...">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Botões de ação -->
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <a href="/" class="btn btn-primary btn-custom">
                            <i class="fas fa-home me-2"></i>Página Inicial
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-primary btn-custom">
                            <i class="fas fa-arrow-left me-2"></i>Voltar
                        </a>
                    </div>

                    <!-- Informações adicionais -->
                    <div class="timestamp">
                        <p class="mb-1">
                            <i class="fas fa-clock me-2"></i>Timestamp: 2025-02-27 20:45:56 UTC
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-user me-2"></i>Usuário: wandersilva5
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Links úteis -->
    <div class="bg-white py-4 fixed-bottom">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-md-auto">
                    <a href="/contact" class="text-decoration-none text-secondary me-4">
                        <i class="fas fa-envelope me-2"></i>Contato
                    </a>
                    <a href="/support" class="text-decoration-none text-secondary me-4">
                        <i class="fas fa-question-circle me-2"></i>Suporte
                    </a>
                    <a href="/sitemap" class="text-decoration-none text-secondary">
                        <i class="fas fa-sitemap me-2"></i>Mapa do Site
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>