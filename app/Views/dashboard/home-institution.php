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


<!-- Resumo em Cards -->
<div class="row mt-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people" style="font-size: 3rem; color: cornflowerblue; padding: 10px;"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Alunos Matriculados</h6>
                        <h3 class="mb-0"><?= $dashboardData['total_students'] ?></h3>
                        <span class="text-success small">
                            <i class="bi bi-arrow-up"></i> 
                            +3.2% este mês
                        </span>
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
                        <i class="bi bi-mortarboard" style="font-size: 3rem; color: cornflowergreen; padding: 10px;"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Professores</h6>
                        <h3 class="mb-0"><?= $dashboardData['total_teachers'] ?></h3>
                        <span class="text-success small">
                            <i class="bi bi-arrow-up"></i> 
                            +2 novos
                        </span>
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
                        <i class="bi bi-calendar-check" style="font-size: 3rem; color: orange; padding: 10px;"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Frequência Média</h6>
                        <h3 class="mb-0"><?= $dashboardData['avg_attendance'] ?>%</h3>
                        <span class="text-danger small">
                            <i class="bi bi-arrow-down"></i> 
                            -1.1% este mês
                        </span>
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
                        <i class="bi bi-cash-stack" style="font-size: 3rem; color: cornflowercyan; padding: 10px;"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-1">Pagamentos Pendentes</h6>
                        <h3 class="mb-0"><?= $dashboardData['pending_payments'] ?></h3>
                        <span class="text-warning small">
                            <i class="bi bi-arrow-up"></i> 
                            +7 esta semana
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendário de Eventos e Comunicados Recentes -->
<div class="row">
    <!-- Calendário/Próximos Eventos -->
    <div class="col-xl-6 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
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
            <div class="card-header d-flex justify-content-between align-items-center">
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

<!-- Gráficos e Estatísticas -->
<div class="row">
    <!-- Desempenho Acadêmico -->
    <div class="col-xl-8 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-bar-chart me-2"></i>Desempenho Acadêmico</h5>
            </div>
            <div class="card-body">
                <canvas id="academicPerformanceChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <!-- Distribuição dos Alunos por Turma -->
    <div class="col-xl-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-pie-chart me-2"></i>Alunos por Turma</h5>
            </div>
            <div class="card-body">
                <canvas id="classDistributionChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- KPIs e Indicadores de Desempenho -->
<div class="row">
    <!-- Frequência por Dia da Semana -->
    <div class="col-xl-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-calendar2-week me-2"></i>Frequência por Dia</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Este Mês
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Esta Semana</a></li>
                        <li><a class="dropdown-item" href="#">Este Mês</a></li>
                        <li><a class="dropdown-item" href="#">Este Trimestre</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <canvas id="weekdayAttendanceChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Indicadores de Desempenho -->
    <div class="col-xl-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-speedometer2 me-2"></i>Indicadores de Desempenho</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Taxa de Aprovação</span>
                        <span class="fw-bold"><?= $performanceKPIs['approval_rate'] ?>%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $performanceKPIs['approval_rate'] ?>%" 
                            aria-valuenow="<?= $performanceKPIs['approval_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Taxa de Frequência</span>
                        <span class="fw-bold"><?= $performanceKPIs['attendance_rate'] ?>%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $performanceKPIs['attendance_rate'] ?>%" 
                            aria-valuenow="<?= $performanceKPIs['attendance_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Satisfação dos Professores</span>
                        <span class="fw-bold"><?= $performanceKPIs['teacher_satisfaction'] ?>%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: <?= $performanceKPIs['teacher_satisfaction'] ?>%" 
                            aria-valuenow="<?= $performanceKPIs['teacher_satisfaction'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Satisfação dos Pais</span>
                        <span class="fw-bold"><?= $performanceKPIs['parent_satisfaction'] ?>%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $performanceKPIs['parent_satisfaction'] ?>%" 
                            aria-valuenow="<?= $performanceKPIs['parent_satisfaction'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Taxa de Evasão</span>
                        <span class="fw-bold"><?= $performanceKPIs['dropout_rate'] ?>%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $performanceKPIs['dropout_rate'] ?>%" 
                            aria-valuenow="<?= $performanceKPIs['dropout_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financeiro -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Fluxo Financeiro</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary active">Semestral</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Anual</button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="financialChart" height="150"></canvas>
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

<?php push('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados acadêmicos
    const academicCtx = document.getElementById('academicPerformanceChart').getContext('2d');
    const academicChart = new Chart(academicCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($academicPerformance['labels']) ?>,
            datasets: [
                {
                    label: 'Média Geral',
                    data: <?= json_encode($academicPerformance['datasets'][0]['data']) ?>,
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Meta',
                    data: <?= json_encode($academicPerformance['datasets'][1]['data']) ?>,
                    borderColor: '#e74a3b',
                    borderDash: [5, 5],
                    backgroundColor: 'transparent',
                    tension: 0.3,
                    fill: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    min: 5,
                    max: 10,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                        text: 'Nota Média'
                    }
                }
            }
        }
    });
    
    // Distribuição de alunos por turma
    const classCtx = document.getElementById('classDistributionChart').getContext('2d');
    const classChart = new Chart(classCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($classDistribution, 'turma')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($classDistribution, 'alunos')) ?>,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });
    
    // Frequência por dia da semana
    const weekdayCtx = document.getElementById('weekdayAttendanceChart').getContext('2d');
    const weekdayChart = new Chart(weekdayCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($attendanceByWeekday['labels']) ?>,
            datasets: [{
                label: 'Frequência (%)',
                data: <?= json_encode($attendanceByWeekday['data']) ?>,
                backgroundColor: 'rgba(78, 115, 223, 0.7)',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    min: 80,
                    max: 100,
                    ticks: {
                        stepSize: 5
                    }
                }
            }
        }
    });
    
    // Financeiro
    const financialCtx = document.getElementById('financialChart').getContext('2d');
    const financialChart = new Chart(financialCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($financialData['labels']) ?>,
            datasets: [
                {
                    label: 'Esperado (R$)',
                    data: <?= json_encode($financialData['expected']) ?>,
                    backgroundColor: 'rgba(78, 115, 223, 0.3)',
                    borderColor: '#4e73df',
                    borderWidth: 1,
                    order: 2
                },
                {
                    label: 'Recebido (R$)',
                    data: <?= json_encode($financialData['received']) ?>,
                    backgroundColor: 'rgba(28, 200, 138, 0.7)',
                    borderColor: '#1cc88a',
                    borderWidth: 1,
                    order: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('pt-BR', { 
                                    style: 'currency', 
                                    currency: 'BRL' 
                                }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 30000,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
});
</script>
<?php endpush() ?>