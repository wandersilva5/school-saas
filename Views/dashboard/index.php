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