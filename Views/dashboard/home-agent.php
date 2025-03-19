<!-- Carrossel de Imagens -->
<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <?php foreach ($sliderImages as $index => $image): ?>
            <button type="button"
                data-bs-target="#carouselExampleIndicators"
                data-bs-slide-to="<?= $index ?>"
                class="<?= $index === 0 ? 'active' : '' ?>"
                aria-current="<?= $index === 0 ? 'true' : 'false' ?>"
                aria-label="Slide <?= $index + 1 ?>">
            </button>
        <?php endforeach; ?>
    </div>
    <div class="carousel-inner">
        <?php foreach ($sliderImages as $index => $image): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <img src="<?= base_url('uploads/slider/' . $image['image_url']) ?>"
                    class="d-block w-100"
                    alt="<?= htmlspecialchars($image['title'] ?? 'Slide ' . ($index + 1)) ?>">
            </div>
        <?php endforeach; ?>
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
<div class="row mt-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="ms-3">
                        <h6 class="mb-1">Entradas Hoje</h6>
                        <small class="detail-label">Entradas de funcionarios</small>
                        <h3 class="mb-0"><?= $dashboardData['entradas_hoje'] ?></h3>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-door-open" style="font-size: 3rem; color: cornflowerblue; padding: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                <div class="ms-3">
                        <h6 class="mb-1">Saídas Hoje</h6>
                        <small class="detail-label">Saídas de funcionarios</small>
                        <h3 class="mb-0"><?= $dashboardData['saidas_hoje'] ?></h3>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-door-closed" style="font-size: 3rem; color: cornflowergreen; padding: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                <div class="ms-3">
                        <h6 class="mb-1">Visitantes</h6>
                        <small class="detail-label">Entraram hoje</small>
                        <h3 class="mb-0"><?= $dashboardData['visitantes'] ?></h3>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-person-badge" style="font-size: 3rem; color: orange; padding: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                <div class="ms-3">
                        <h6 class="mb-1">Alertas</h6>
                        <small class="detail-label">Entradas não autorizadas</small>
                        <h3 class="mb-0"><?= $dashboardData['alertas'] ?></h3>
                    </div>
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-exclamation-triangle" style="font-size: 3rem; color: red; padding: 10px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-md-12 mb-4">
        <div class="content-section">
            <h5 class="section-title">Registros Recentes</h5>
            <div class="table-responsive">
                <?php
                if (empty($recentRecords)) {
                    echo "<p>Nenhum registro encontrado.</p>";
                }
                ?>
                <table class="table custom-table">
                    <thead>
                        <tr>
                            <th>Horário</th>
                            <th>Tipo</th>
                            <th>Nome</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentRecords as $record): ?>
                            <tr>
                                <td><span class="time-badge"><?= $record['time'] ?></span></td>
                                <td><?= $record['type'] ?></td>
                                <td><?= $record['name'] ?></td>
                                <td>
                                    <span class="status-badge <?= strtolower($record['status']) ?>">
                                        <?= $record['status'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-12 mb-4">
        <div class="content-section">
            <h5 class="section-title">Autorizações Pendentes</h5>
            <div class="auth-cards">
                <?php foreach ($pendingAuths as $auth): ?>
                    <div class="auth-card">
                        <div class="auth-card-header">
                            <div class="auth-info">
                                <h6 class="auth-name"><?= $auth['name'] ?></h6>
                                <span class="type-badge"><?= $auth['type'] ?></span>
                            </div>
                            <div class="action-buttons">
                                <button class="btn-action approve" title="Aprovar">
                                    <i class="bi bi-check"></i>
                                </button>
                                <button class="btn-action reject" title="Rejeitar">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="auth-detail">
                                <span class="detail-label">Motivo: <span class="detail-value"><?= $auth['reason'] ?></span></span>
                            </div>
                            <div class="auth-detail">
                                <span class="detail-label">Solicitante: <span class="detail-value"><?= $auth['requested_by'] ?></span></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php push('styles') ?>
<style>
    /* Carousel section */
    #carouselExampleIndicators {
        margin-top: -45px;
        margin-left: -45px;
        margin-right: -45px;
        width: calc(100% + 90px);
        position: relative;
        height: 444px;
    }

    #carouselExampleIndicators .carousel-item img {
        width: 100%;
        height: 444px;
        object-fit: cover;
    }

    .carousel-control-prev,
    .carousel-control-next {
        z-index: 100;
    }

    .carousel-inner {
        width: 100%;
        height: 444px;
    }

    /* Layout adjustments */
    .container-fluid {
        padding: 1.5rem;
    }
</style>
<?php endpush() ?>