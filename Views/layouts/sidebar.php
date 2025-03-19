<?php

use App\Helpers\AuthHelper; ?>
<?php
// No início do arquivo, após o uso do AuthHelper
$userRoles = $_SESSION['user']['roles'] ?? [];
error_log("Roles do usuário no sidebar: " . print_r($userRoles, true));
?>
<div class="d-flex flex-column flex-shrink-0 text-white">
    <nav id="sidebar" class="sidebar">
        <div class="sidebar-content">
            <div class="sidebar-brand">
                <div class="brand-content">
                    <?php
                    // Busca os dados da instituição
                    $institutionId = $_SESSION['user']['institution_id'] ?? null;
                    $db = \App\Config\Database::getInstance()->getConnection();
                    $stmt = $db->prepare("SELECT name, logo_url FROM institutions WHERE id = ?");
                    $stmt->execute([$institutionId]);
                    $institution = $stmt->fetch(\PDO::FETCH_ASSOC);
                    ?>

                    <div class="institution-logo">
                        <?php if ($institution['logo_url']): ?>
                            <img src="<?= htmlspecialchars($institution['logo_url']) ?>" alt="Logo" class="img-fluid">
                        <?php else: ?>
                            <i class="bi bi-building"></i>
                        <?php endif; ?>
                    </div>
                    <div class="institution-name">
                        <?= htmlspecialchars($institution['name'] ?? 'School SaaS') ?>
                    </div>
                </div>
            </div>

            <ul class="sidebar-nav">
                <li class="sidebar-header">
                    Principal
                </li>
                <?php if (in_array('Master', $userRoles)): ?>
                    <li class="sidebar-item <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        <a class="sidebar-link" href="/dashboard">
                            <i class="bi bi-house-door"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (!in_array('Master', $userRoles)): ?>
                    <li class="sidebar-item <?= $currentPage === 'dashboard-institution' ? 'active' : '' ?>">
                        <a class="sidebar-link" href="/dashboard-institution">
                            <i class="bi bi-house-door"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="sidebar-item <?= $currentPage === 'calendar' ? 'active' : '' ?>">
                    <a class="sidebar-link" href="/calendar">
                        <i class="bi bi-calendar3"></i>
                        <span>Calendário</span>
                    </a>
                </li>

                <li class="sidebar-header">
                    Acadêmico
                </li>
                <li class="sidebar-item <?= $currentPage === 'materias' ? 'active' : '' ?>">
                    <a class="sidebar-link" href="/materias">
                        <i class="bi bi-book"></i>
                        <span>Matérias</span>
                    </a>
                </li>
                <li class="sidebar-item <?= $currentPage === 'classes' ? 'active' : '' ?>">
                    <a class="sidebar-link" href="/classes">
                        <i class="bi bi-people"></i>
                        <span>Turmas</span>
                    </a>
                </li>
                <li class="sidebar-item <?= $currentPage === 'alunos' ? 'active' : '' ?>">
                    <a class="sidebar-link" href="/alunos">
                        <i class="bi bi-person-badge"></i>
                        <span>Alunos</span>
                    </a>
                </li>
                <li class="sidebar-item <?= $currentPage === 'responsaveis' ? 'active' : '' ?>">
                    <a class="sidebar-link" href="/responsaveis">
                        <i class="bi bi-person-badge"></i>
                        <span>Responsáveis</span>
                    </a>
                </li>

                <?php if (in_array('TI', $userRoles)): ?>
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

                <?php endif;
                ?>
                <?php if (in_array('Master', $userRoles)):
                ?>
                    <li class="sidebar-header">
                        Configurações do Sistema
                    </li>
                    <li class="sidebar-item <?= $currentPage === 'access-management' ? 'active' : '' ?>">
                        <a class="sidebar-link" href="/access-management">
                            <i class="bi bi-shield-lock"></i>
                            <span>Gerenciar Acessos</span>
                        </a>
                    </li>
                    
                <?php endif; ?>
                <?php if (in_array('Master', $_SESSION['user']['roles'])): ?>
                    <li class="sidebar-item <?= $currentPage === 'institution' ? 'active' : '' ?>">
                        <a class="sidebar-link" href="/institution">
                            <i class="bi bi-building"></i>
                            <span>Instituições</span>
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    </nav>
</div>


<?php push('scripts') ?>
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
<?php endpush() ?>