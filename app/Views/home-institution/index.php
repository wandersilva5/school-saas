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


<!-- Calendário de Eventos e Comunicados Recentes -->
<div class="row mt-4">
    <!-- Calendário/Próximos Eventos -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-calendar-event me-2"></i>Próximos Eventos</h5>
                <button class="btn btn-sm btn-primary">Ver Calendário</button>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <?php foreach ($upcomingEvents as $event): ?>
                        <div class="list-group-item list-group-item-action d-flex gap-3 py-3">
                            <div class="bg-primary rounded-3 text-white text-center" style="min-width: 50px; padding: 10px 5px;">
                                <div class="fs-4 fw-bold"><?= date('d', strtotime($event['data'])) ?></div>
                                <div class="small"><?= date('M', strtotime($event['data'])) ?></div>
                            </div>
                            <div class="d-flex gap-2 w-100 justify-content-between">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($event['titulo']) ?></h6>
                                    <p class="mb-0 text-muted"><?= htmlspecialchars($event['horario']) ?></p>
                                </div>
                                <small class="text-nowrap">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Detalhes</button>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="/calendar" class="btn btn-sm btn-outline-primary">Ver todos os eventos</a>
            </div>
        </div>
    </div>

    <!-- Comunicados Recentes -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-megaphone me-2"></i>Comunicados Recentes</h5>
                <button class="btn btn-sm btn-primary">Novo Comunicado</button>
            </div>
            <div class="card-body">
                <?php foreach ($recentAnnouncements as $announcement): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-1"><?= htmlspecialchars($announcement['titulo']) ?></h6>
                            <span class="badge bg-primary rounded-pill">
                                <?= date('d/m', strtotime($announcement['data'])) ?>
                            </span>
                        </div>
                        <p class="mb-0 text-muted"><?= htmlspecialchars($announcement['conteudo']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="card-footer text-center">
                <a href="#" class="btn btn-sm btn-outline-primary">Ver todos os comunicados</a>
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
        height: 444px;
    }

    /* Fix carousel image height */
    #carouselExampleIndicators .carousel-item img {
        width: 100%;
        height: 444px;
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
        height: 444px;
    }
</style>
<?php endpush() ?>