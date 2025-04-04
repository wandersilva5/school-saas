<nav class="navbar navbar-expand-lg rounded">
    <div class="container-fluid">
        <!-- Brand/logo -->
        <a class="navbar-brand" href="/dashboard">
            <?= $_SESSION['institution_name'] ?? 'Portal Escola' ?>
        </a>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
                <!-- Notificações de Alertas -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="position-absolute top-0 start-80 translate-middle badge rounded-pill bg-danger" id="alertsCount" style="display: none;">
                            0
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0" aria-labelledby="notificationsDropdown">
                        <div class="dropdown-menu-header py-2 px-3 bg-light border-bottom">
                            <strong>Alertas</strong>
                        </div>
                        <div class="list-group alerts-list max-height-300 overflow-auto">
                            <div class="text-center p-2">
                                <div class="spinner-border spinner-border-sm" role="status">
                                    <span class="visually-hidden">Carregando...</span>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-menu-footer py-2 px-3 border-top text-center">
                            <a href="/alerts" class="text-decoration-none">Ver todos alertas</a>
                        </div>
                    </div>
                </li>

                <!-- Perfil do usuário -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= $_SESSION['user']['name'] ?? 'Usuário' ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="/profile"><i class="bi bi-person"></i> Perfil</a></li>
                        <li><a class="dropdown-item" href="/settings"><i class="bi bi-gear"></i> Configurações</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>