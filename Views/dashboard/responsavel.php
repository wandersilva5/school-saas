    <!-- Carousel -->
    <?php if (!empty($sliderImages)): ?>
        <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <?php foreach ($sliderImages as $index => $image): ?>
                    <button type="button"
                        data-bs-target="#carouselExampleCaptions"
                        data-bs-slide-to="<?= $index ?>"
                        class="<?= $index === 0 ? 'active' : '' ?>"
                        aria-current="<?= $index === 0 ? 'true' : 'false' ?>"
                        aria-label="Slide <?= $index + 1 ?>">
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="carousel-inner rounded">
                <?php foreach ($sliderImages as $index => $image): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= base_url('uploads/slider/' . $image['image_url']) ?>" class="d-block w-100" alt="Slide <?= $index + 1 ?>">
                        <?php if (!empty($image['caption'])): ?>
                            <div class="carousel-caption d-none d-md-block">
                                <h5><?= $image['caption'] ?></h5>
                                <?php if (!empty($image['description'])): ?>
                                    <p><?= $image['description'] ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    <?php endif; ?>
    <!-- Content Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><?= $pageTitle ?></h1>
            <p class="text-muted small mb-0">Acompanhe as informações do seu filho e da escola</p>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-warning" role="alert">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($alunos)): ?>
        <!-- Navegação entre os filhos -->
        <ul class="nav nav-pills mb-4" id="childrenTab" role="tablist">
            <?php foreach ($alunos as $index => $aluno): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?= $index === 0 ? 'active' : '' ?>"
                        id="child-<?= $aluno['id'] ?>-tab"
                        data-bs-toggle="pill"
                        data-bs-target="#child-<?= $aluno['id'] ?>"
                        type="button"
                        role="tab">
                        <?= $aluno['nome'] ?>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="tab-content" id="childrenTabContent">
            <?php foreach ($alunos as $index => $aluno): ?>
                <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>"
                    id="child-<?= $aluno['id'] ?>"
                    role="tabpanel">
                    <div class="row g-4">
                        <!-- Informações do Aluno -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white border-0 pt-4 pb-0">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-primary text-white me-3">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0"><?= $aluno['nome'] ?></h5>
                                            <p class="text-muted small mb-0">Informações do Aluno</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="info-item">
                                                <span class="text-muted small d-block">Matrícula</span>
                                                <strong><?= $aluno['matricula'] ?></strong>
                                            </div>
                                        </div>
                                        <!-- Adiciona novas informações -->
                                        <div class="col-sm-6">
                                            <div class="info-item">
                                                <span class="text-muted small d-block">Data Nascimento</span>
                                                <strong><?= date('d/m/Y', strtotime($aluno['data_nascimento'])) ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="info-item">
                                                <span class="text-muted small d-block">Turma</span>
                                                <strong><?= $aluno['turma'] ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="info-item">
                                                <span class="text-muted small d-block">Turno</span>
                                                <strong><?= $aluno['turno'] ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="info-item">
                                                <span class="text-muted small d-block">Frequência</span>
                                                <strong class="text-success"><?= $aluno['frequencia'] ?></strong>
                                            </div>
                                        </div>
                                        <?php if (!empty($aluno['observacoes_saude'])): ?>
                                            <div class="col-12">
                                                <div class="info-item">
                                                    <span class="text-muted small d-block">Observações de Saúde</span>
                                                    <strong class="text-danger"><?= $aluno['observacoes_saude'] ?></strong>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="mt-4">
                                        <h6 class="border-bottom pb-2">Últimas Notas</h6>
                                        <div class="row g-2 mt-2">
                                            <?php foreach ($aluno['notas'] as $disciplina => $nota): ?>
                                                <div class="col-sm-6">
                                                    <div class="p-2 bg-light rounded">
                                                        <span class="text-muted small d-block"><?= $disciplina ?></span>
                                                        <strong class="text-primary"><?= $nota ?></strong>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financeiro (específico para cada filho) -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-white border-0 pt-4 pb-0">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="icon-circle bg-success text-white me-3">
                                            <i class="bi bi-cash"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">Financeiro - <?= $aluno['nome'] ?></h5>
                                            <p class="text-muted small mb-0">Mensalidades e Pagamentos</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Adicione uma navegação por mês/ano -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-chevron-left"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm px-3">
                                                2024
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <?php if (!empty($financeiro[$aluno['id']])): ?>
                                        <?php foreach ($financeiro[$aluno['id']] as $mensalidade): ?>
                                            <div class="payment-item mb-3 p-3 bg-light rounded">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1"><?= $mensalidade['mes'] ?? 'Mês não informado' ?></h6>
                                                        <p class="text-muted small mb-0">
                                                            Vence em <?= $mensalidade['vencimento'] ?? 'Data não informada' ?>
                                                        </p>
                                                    </div>
                                                    <div class="text-end">
                                                        <h6 class="mb-1">
                                                            R$ <?= isset($mensalidade['valor']) ? number_format($mensalidade['valor'], 2, ',', '.') : '0,00' ?>
                                                        </h6>
                                                        <span class="badge <?= ($mensalidade['status'] ?? '') == 'Pago' ? 'bg-success' : 'bg-warning' ?>">
                                                            <?= $mensalidade['status'] ?? 'Pendente' ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center text-muted py-4">
                                            <i class="bi bi-cash-coin fs-2 d-block mb-3"></i>
                                            <p class="mb-0">Nenhuma mensalidade encontrada</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            Nenhum aluno encontrado vinculado ao seu perfil. Por favor, entre em contato com a instituição.
        </div>
    <?php endif; ?>

    <!-- Comunicados e Eventos -->
    <div class="row g-4">
        <!-- Comunicados -->
        <div class="col-md-6 mb-4">
            <?php if (!empty($comunicados)): ?>
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-info text-white me-3">
                                <i class="bi bi-megaphone"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Comunicados</h5>
                                <p class="text-muted small mb-0">Últimas notificações da escola</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php foreach ($comunicados as $comunicado): ?>
                            <div class="notice-item mb-3 p-3 bg-light rounded">
                                <h6 class="mb-2"><?= $comunicado['titulo'] ?></h6>
                                <p class="text-muted small mb-2">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= date('d/m/Y', strtotime($comunicado['data'])) ?>
                                </p>
                                <p class="mb-0"><?= $comunicado['descricao'] ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center text-muted">
                        <i class="bi bi-bell-slash fs-2"></i>
                        <p class="mt-2 mb-0">Nenhum comunicado disponível.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Eventos -->
        <div class="col-md-6 mb-4">
            <?php if (!empty($eventos)): ?>
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-circle bg-warning text-white me-3">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div>
                                <h5 class="mb-0">Eventos</h5>
                                <p class="text-muted small mb-0">Próximas atividades escolares</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php foreach ($eventos as $evento): ?>
                            <div class="event-item mb-3 p-3 bg-light rounded">
                                <h6 class="mb-2"><?= $evento['titulo'] ?></h6>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    <?= date('d/m/Y', strtotime($evento['data'])) ?>
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= $evento['horario'] ?>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center text-muted">
                        <i class="bi bi-calendar-x fs-2"></i>
                        <p class="mt-2 mb-0">Nenhum evento programado.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Estilos customizados -->
    <?php push('styles') ?>
    <style>
        .icon-circle {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.2rem;
        }

        .card {
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .info-item {
            padding: 0.5rem;
            background-color: rgba(0, 0, 0, 0.03);
            border-radius: 0.375rem;
        }

        .payment-item,
        .notice-item,
        .event-item {
            transition: all 0.2s ease-in-out;
        }

        .payment-item:hover,
        .notice-item:hover,
        .event-item:hover {
            transform: translateX(5px);
            background-color: rgba(0, 0, 0, 0.05) !important;
        }

        .bg-light {
            background-color: rgba(0, 0, 0, 0.03) !important;
        }

        /* Estilos adicionais para as pills */
        .nav-pills .nav-link {
            color: #6c757d;
            background-color: transparent;
            border: 1px solid #dee2e6;
            margin-right: 0.5rem;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease-in-out;
        }

        .nav-pills .nav-link:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }

        .nav-pills .nav-link.active {
            color: #fff;
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        /* Suaviza a transição entre tabs */
        .tab-content>.tab-pane {
            transition: all 0.2s ease-in-out;
        }

        .tab-content>.tab-pane.active {
            animation: fadeIn 0.2s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Target only the carousel section */
        #carouselExampleCaptions {
            margin-top: -45px;
            margin-left: -45px;
            margin-right: -45px;
            width: calc(100% + 90px);
            position: relative;
            height: 444px;
        }

        /* Fix carousel image height */
        #carouselExampleCaptions .carousel-item img {
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

    <?php push('scripts') ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializa os tooltips do Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
    <?php endpush() ?>