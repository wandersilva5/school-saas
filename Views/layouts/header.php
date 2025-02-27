<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Botão para toggle do sidebar -->
        <button class="btn" id="sidebar-toggle">
            <i class="bi bi-list"></i>
        </button>

        <!-- Brand/logo -->
        <a class="navbar-brand mx-3" href="/dashboard">
            <?= $_SESSION['institution_name'] ?? 'Portal Escolar' ?>
        </a>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto">
                <!-- Notificações -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-bell"></i>
                        <span class="badge bg-danger">3</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                        <li><a class="dropdown-item" href="#">Notificação 1</a></li>
                        <li><a class="dropdown-item" href="#">Notificação 2</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Ver todas</a></li>
                    </ul>
                </li>

                <!-- Perfil do usuário -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= $_SESSION['user_name'] ?? 'Usuário' ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="/profile"><i class="bi bi-person"></i> Perfil</a></li>
                        <li><a class="dropdown-item" href="/settings"><i class="bi bi-gear"></i> Configurações</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>