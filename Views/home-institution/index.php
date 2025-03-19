<!-- Carrossel de Imagens -->
<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
    </div>
    <div class="carousel-inner" style="width: 100%; max-height: 680px; position: relative; ">
        <div class="carousel-item active">
            <img src="https://img.freepik.com/fotos-gratis/menino-de-copia-espaco-com-livros-mostrando-sinal-ok_23-2148469950.jpg" class="d-block w-100" alt="Imagem 1">
        </div>
        <div class="carousel-item">
            <img src="https://img.freepik.com/fotos-gratis/alunos-sabendo-a-resposta-certa_329181-14271.jpg" class="d-block w-100" alt="Imagem 2">
        </div>
        <div class="carousel-item">
            <img src="https://img.freepik.com/fotos-gratis/estudante-feliz-com-sua-mochila-e-livros_1098-3454.jpg" class="d-block w-100" alt="Imagem 3">
        </div>
        <div class="carousel-item">
            <img src="https://img.freepik.com/fotos-gratis/livro-com-fundo-de-placa-verde_1150-3837.jpg" class="d-block w-100" alt="Imagem 4">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
<!-- Fim do Carrossel de Imagens -->
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-people" style="font-size: 3rem; color: cornflowerblue; padding: 10px;"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-1">Total de Usuários</h6>
                            <h3 class="mb-0"><?= $dashboardData['total_users'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-book" style="font-size: 3rem; color: cornflowergreen; padding: 10px;"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-1">Cursos</h6>
                            <h3 class="mb-0"><?= $dashboardData['total_courses'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-calendar3" style="font-size: 3rem; color: cornflowerorange; padding: 10px;"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-1">Turmas Ativas</h6>
                            <h3 class="mb-0"><?= $dashboardData['active_classes'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-person-plus" style="font-size: 3rem; color: cornflowercyan; padding: 10px;"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-1">Novos Alunos</h6>
                            <h3 class="mb-0"><?= $dashboardData['new_students'] ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Bem-vindo ao Sistema</h5>
                    <p class="card-text">Este é seu painel de controle. Aqui você pode gerenciar todos os aspectos do sistema.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php push('styles') ?>
<style>
    /* Target only the carousel section */
    #carouselExampleIndicators {
        margin-top: -45px;
        margin-left: -45px;
        margin-right: -45px;
        width: calc(100% + 90px);
        position: relative;
    }

    /* Fix carousel image height */
    #carouselExampleIndicators .carousel-item img {
        width: 100%;
        max-height: 680px;
        object-fit: cover;
    }

    /* Stats cards section */
    .container-fluid {
        padding: 1.5rem;
    }

    /* Ensure carousel controls are visible */
    .carousel-control-prev,
    .carousel-control-next {
        z-index: 100;
    }

    /* Remove inline styles from carousel-inner */
    .carousel-inner {
        width: 100%;
        height: 680px;
    }
</style>
<?php endpush() ?>