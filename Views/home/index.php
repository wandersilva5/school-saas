<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Escola - Gestão Educacional Inteligente</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/home.css') ?>" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-transparent fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="<?= base_url('assets/images/logo-nome-branco.png') ?>" alt="Portal Escola Logo" height="80">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#recursos">Recursos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#beneficios">Benefícios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#precos">Preços</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contato">Contato</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Entre</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-3" href="#demo">Agendar Demo</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
        <div class="container mt-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Transforme a gestão da sua escola com o Portal Escola</h1>
                    <p class="lead mb-4">Simplifique processos, melhore a comunicação e potencialize o aprendizado com nossa plataforma completa de gestão escolar.</p>
                    <div class="d-flex gap-3">
                        <a href="#demo" class="btn btn-light btn-lg">Solicitar Demo</a>
                        <a href="#recursos" class="btn btn-outline-light btn-lg">Saiba Mais</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="https://via.placeholder.com/600x400" alt="Portal Escola Dashboard" class="img-fluid">
                </div>
            </div>
        </div>
        <svg class="hero-wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffffff" d="M0,96L48,112C96,128,192,160,288,165.3C384,171,480,149,576,128C672,107,768,85,864,96C960,107,1056,149,1152,154.7C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </section>

    <!-- Recursos Section -->
    <section id="recursos" class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Recursos Principais</h2>
                <p class="lead text-muted">Tudo que você precisa para uma gestão escolar eficiente</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <img src="https://via.placeholder.com/80" alt="Gestão Acadêmica" class="feature-icon">
                            <h3 class="h5 mb-3">Gestão Acadêmica</h3>
                            <p>Controle notas, frequência e desempenho dos alunos de forma simples e eficiente.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <img src="https://via.placeholder.com/80" alt="Comunicação" class="feature-icon">
                            <h3 class="h5 mb-3">Comunicação Integrada</h3>
                            <p>Mantenha pais, alunos e professores conectados através de nossa plataforma.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <img src="https://via.placeholder.com/80" alt="Financeiro" class="feature-icon">
                            <h3 class="h5 mb-3">Gestão Financeira</h3>
                            <p>Controle mensalidades, despesas e gere relatórios financeiros completos.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Preços Section -->
    <section id="precos" class="section-padding bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Planos e Preços</h2>
                <p class="lead text-muted">Escolha o melhor plano para sua instituição</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card pricing-card h-100">
                        <div class="card-body text-center">
                            <h3 class="mb-4">Básico</h3>
                            <h2 class="display-4 fw-bold mb-4">R$ 299<small class="fs-6">/mês</small></h2>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2">Até 200 alunos</li>
                                <li class="mb-2">Gestão acadêmica básica</li>
                                <li class="mb-2">Suporte por email</li>
                                <li class="mb-2">Portal do aluno</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary">Começar Agora</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card pricing-card h-100 border-primary">
                        <div class="card-body text-center">
                            <span class="badge bg-primary mb-2">Mais Popular</span>
                            <h3 class="mb-4">Profissional</h3>
                            <h2 class="display-4 fw-bold mb-4">R$ 499<small class="fs-6">/mês</small></h2>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2">Até 500 alunos</li>
                                <li class="mb-2">Gestão acadêmica completa</li>
                                <li class="mb-2">Suporte prioritário</li>
                                <li class="mb-2">Módulo financeiro</li>
                            </ul>
                            <a href="#" class="btn btn-primary">Começar Agora</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card pricing-card h-100">
                        <div class="card-body text-center">
                            <h3 class="mb-4">Enterprise</h3>
                            <h2 class="display-4 fw-bold mb-4">Sob Consulta</h2>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2">Alunos ilimitados</li>
                                <li class="mb-2">Personalização completa</li>
                                <li class="mb-2">Suporte 24/7</li>
                                <li class="mb-2">API disponível</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary">Falar com Consultor</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contato Section -->
    <section id="contato" class="section-padding">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="fw-bold mb-4">Entre em contato</h2>
                    <p class="lead mb-4">Estamos prontos para ajudar sua escola a crescer</p>
                    <form>
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Nome completo">
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Email">
                        </div>
                        <div class="mb-3">
                            <input type="tel" class="form-control" placeholder="Telefone">
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control" rows="4" placeholder="Mensagem"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar Mensagem</button>
                    </form>
                </div>
                <div class="col-lg-6">
                    <img src="https://via.placeholder.com/600x400" alt="Contato" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Sobre o Portal Escola</h5>
                    <p class="mb-0">Transformando a gestão escolar através da tecnologia e inovação.</p>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="mb-3">Links Rápidos</h5>
                    <ul class="list-unstyled">
                        <li><a href="#recursos" class="text-white-50">Recursos</a></li>
                        <li><a href="#precos" class="text-white-50">Preços</a></li>
                        <li><a href="#contato" class="text-white-50">Contato</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Contato</h5>
                    <ul class="list-unstyled text-white-50">
                        <li>contato@portalescola.com.br</li>
                        <li>+55 (11) 9999-9999</li>
                        <li>São Paulo, SP</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center text-white-50">
                <p class="mb-0">&copy; 2025 Portal Escola. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar');

            const handleScroll = () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                    navbar.style.backgroundColor = getComputedStyle(document.documentElement)
                        .getPropertyValue('--secondary-color');
                } else {
                    navbar.classList.remove('scrolled');
                    navbar.style.backgroundColor = 'transparent';
                }
            };

            // Add scroll event listener
            window.addEventListener('scroll', handleScroll);

            // Call handleScroll on page load
            handleScroll();
        });
    </script>
</body>

</html