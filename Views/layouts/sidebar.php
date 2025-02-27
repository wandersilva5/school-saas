<?php use App\Helpers\AuthHelper; ?>
<nav id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <div class="sidebar-brand">
            <i class="bi bi-building"></i>
            <span class="align-middle ms-2">School SaaS</span>
        </div>

        <ul class="sidebar-nav">
            <li class="sidebar-header">
                Principal
            </li>
            <li class="sidebar-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <a class="sidebar-link" href="/dashboard">
                    <i class="bi bi-house-door"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentPage === 'calendar' ? 'active' : '' ?>">
                <a class="sidebar-link" href="/calendar">
                    <i class="bi bi-calendar3"></i>
                    <span>Calendário</span>
                </a>
            </li>

            <li class="sidebar-header">
                Acadêmico
            </li>
            <li class="sidebar-item <?= $currentPage === 'courses' ? 'active' : '' ?>">
                <a class="sidebar-link" href="/courses">
                    <i class="bi bi-book"></i>
                    <span>Cursos</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentPage === 'classes' ? 'active' : '' ?>">
                <a class="sidebar-link" href="/classes">
                    <i class="bi bi-people"></i>
                    <span>Turmas</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentPage === 'students' ? 'active' : '' ?>">
                <a class="sidebar-link" href="/students">
                    <i class="bi bi-person-badge"></i>
                    <span>Alunos</span>
                </a>
            </li>

            <?php if (in_array('TI', $_SESSION['user']['roles'] ?? [])): ?>
            <li class="sidebar-header">
                Administração
            </li>
            <li class="sidebar-item <?= $currentPage === 'users' ? 'active' : '' ?>">
                <a class="sidebar-link" href="/users">
                    <i class="bi bi-people-fill"></i>
                    <span>Usuários</span>
                </a>
            </li>
            <li class="sidebar-item <?= $currentPage === 'settings' ? 'active' : '' ?>">
                <a class="sidebar-link" href="/settings">
                    <i class="bi bi-gear"></i>
                    <span>Configurações</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<style>
.sidebar {
    min-height: 100vh;
    background: #154A9A;
    color: rgba(255, 255, 255, 0.9);
    width: 260px;
    position: fixed;
    top: 0;
    left: 0;
    transition: all 0.3s;
    z-index: 100;
}

.sidebar-content {
    padding: 1.5rem;
}

.sidebar-brand {
    padding: 1rem 0;
    font-size: 1.15rem;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.sidebar-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-header {
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.75rem;
    padding: 1.5rem 0 0.375rem;
    text-transform: uppercase;
    font-weight: 600;
}

.sidebar-item {
    position: relative;
}

.sidebar-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    border-radius: 0.5rem;
    transition: all 0.2s;
    margin: 0.2rem 0;
}

.sidebar-link i {
    margin-right: 0.75rem;
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.5);
    transition: all 0.2s;
}

.sidebar-link:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
}

.sidebar-link:hover i {
    color: #fff;
}

.sidebar-item.active .sidebar-link {
    color: #fff;
    background: rgba(255, 255, 255, 0.15);
    font-weight: 500;
}

.sidebar-item.active .sidebar-link i {
    color: #fff;
}

@media (max-width: 768px) {
    .sidebar {
        margin-left: -260px;
    }
    
    .sidebar.toggled {
        margin-left: 0;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adiciona classe active ao clicar no item do menu
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            document.querySelector('.sidebar-item.active')?.classList.remove('active');
            this.closest('.sidebar-item').classList.add('active');
        });
    });
});
</script>