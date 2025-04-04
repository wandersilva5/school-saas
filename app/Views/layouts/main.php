<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Portal Escolar' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/assets/images/logo-azul.png">
    <link href="<?= base_url('assets/css/custom-theme.css') ?>" rel="stylesheet">
    <!-- Page Specific Styles -->
    <?= render_styles() ?>
</head>

<body>
    <?php if (isset($_SESSION['user'])): ?>
        <div class="wrapper">
            <!-- Sidebar -->
            <?php require_once __DIR__ . '/sidebar.php'; ?>
            <div class="main">
                <!-- Header -->
                <?php require_once __DIR__ . '/header.php'; ?>

                <!-- Content -->
                <main class="content">
                    <div class="container-fluid">
                        <?= $content ?>
                    </div>
                </main>

                <!-- Footer -->
                <?php require_once __DIR__ . '/footer.php'; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Login/Register pages -->
        <?= $content ?>
    <?php endif; ?>

    <!-- Toast Container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <div id="toast-notification" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto toast-title">Notificação</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <!-- Toast message will be inserted here -->
            </div>
        </div>
    </div>
    <?php if (isset($_SESSION['toast'])): ?>
        <script>
            // Display toast message if one is set in the session
            document.addEventListener('DOMContentLoaded', function() {
                const toastEl = document.getElementById('toast-notification');
                const toast = new bootstrap.Toast(toastEl);

                // Set toast classes based on type
                const toastType = "<?php echo $_SESSION['toast']['type']; ?>";
                const toastMessage = "<?php echo $_SESSION['toast']['message']; ?>";

                // Set background color based on type
                toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
                if (toastType === 'success') {
                    toastEl.classList.add('bg-success', 'text-white');
                } else if (toastType === 'error') {
                    toastEl.classList.add('bg-danger', 'text-white');
                } else if (toastType === 'warning') {
                    toastEl.classList.add('bg-warning');
                } else if (toastType === 'info') {
                    toastEl.classList.add('bg-info');
                }

                // Set the message
                document.querySelector('.toast-body').textContent = toastMessage;

                // Show the toast
                toast.show();
            });
        </script>
    <?php
        // Clear the toast from session after displaying
        unset($_SESSION['toast']);
    endif;
    ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/assets/js/script.js"></script>
    <script>
        // Função para carregar alertas do usuário
        function loadUserAlerts() {
            fetch('/alerts/user-alerts?limit=5', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const alertsList = document.querySelector('.alerts-list');
                    alertsList.innerHTML = '';

                    if (data.alerts && data.alerts.length > 0) {
                        document.getElementById('alertsCount').textContent = data.count;
                        document.getElementById('alertsCount').style.display = 'inline';

                        data.alerts.forEach(alert => {
                            // Verificar se o alerta já foi lido
                            const isRead = localStorage.getItem(`alert_read_${alert.id}`) === 'true';

                            const alertItem = document.createElement('a');
                            alertItem.href = '#';
                            alertItem.className = `list-group-item list-group-item-action ${isRead ? 'bg-light' : 'bg-white border-start border-4 border-primary'}`;
                            alertItem.onclick = function(e) {
                                e.preventDefault();
                                markAlertAsRead(alert.id);
                                viewAlertDetails(alert);
                            };

                            // Criar conteúdo do alerta
                            alertItem.innerHTML = `
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${alert.title} ${!isRead ? '<span class="badge bg-primary rounded-circle ms-1" style="width: 8px; height: 8px; padding: 0;"></span>' : ''}</h6>
                        <small>${formatDate(alert.created_at)}</small>
                    </div>
                    <p class="mb-1 text-truncate">${alert.message}</p>
                `;

                            alertsList.appendChild(alertItem);
                        });
                    } else {
                        document.getElementById('alertsCount').style.display = 'none';

                        const noAlerts = document.createElement('div');
                        noAlerts.className = 'text-center p-2';
                        noAlerts.textContent = 'Nenhum alerta';
                        alertsList.appendChild(noAlerts);
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar alertas:', error);
                    const alertsList = document.querySelector('.alerts-list');
                    alertsList.innerHTML = '<div class="text-center p-2">Erro ao carregar alertas</div>';
                });
        }

        // Função para marcar alerta como lido
        function markAlertAsRead(alertId) {
            localStorage.setItem(`alert_read_${alertId}`, 'true');
            const alertElement = document.querySelector(`.alert-item-${alertId}`);
            if (alertElement) {
                alertElement.classList.remove('border-start', 'border-4', 'border-primary');
                alertElement.classList.add('bg-light');
                const indicator = alertElement.querySelector('.badge.bg-primary.rounded-circle');
                if (indicator) {
                    indicator.remove();
                }
            }

            // Atualizar contador de alertas não lidos
            updateUnreadCount();
        }

        // Função para atualizar contador de alertas não lidos
        function updateUnreadCount() {
            const alertItems = document.querySelectorAll('.alerts-list .list-group-item');
            let unreadCount = 0;

            alertItems.forEach(item => {
                if (item.classList.contains('border-primary')) {
                    unreadCount++;
                }
            });

            const alertsCountBadge = document.getElementById('alertsCount');
            if (unreadCount > 0) {
                alertsCountBadge.textContent = unreadCount;
                alertsCountBadge.style.display = 'inline';
            } else {
                alertsCountBadge.style.display = 'none';
            }
        }

        // Função para visualizar detalhes do alerta
        function viewAlertDetails(alert) {
            // Verificar se o modal existe - se não, criar dinamicamente
            let viewModal = document.getElementById('viewAlertModal');
            if (!viewModal) {
                const modalHtml = `
        <div class="modal fade" id="viewAlertModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="view_title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <span id="view_priority_badge" class="badge mb-2"></span>
                            <p><strong>Destinatários:</strong> <span id="view_target_roles"></span></p>
                            <p><strong>Período:</strong> <span id="view_period"></span></p>
                            <p><strong>Criado por:</strong> <span id="view_created_by"></span></p>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <p id="view_message"></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>`;

                document.body.insertAdjacentHTML('beforeend', modalHtml);
                viewModal = document.getElementById('viewAlertModal');
            }

            // Preencher os dados
            document.getElementById('view_title').textContent = alert.title;
            document.getElementById('view_message').textContent = alert.message;
            document.getElementById('view_created_by').textContent = alert.created_by_name || 'Sistema';

            // Definir badge de prioridade
            const priorityBadge = document.getElementById('view_priority_badge');
            let badgeClass = 'bg-info';
            if (alert.priority === 'alta') {
                badgeClass = 'bg-danger';
            } else if (alert.priority === 'média') {
                badgeClass = 'bg-warning';
            } else if (alert.priority === 'baixa') {
                badgeClass = 'bg-success';
            }
            priorityBadge.className = `badge ${badgeClass}`;
            priorityBadge.textContent = alert.priority.charAt(0).toUpperCase() + alert.priority.slice(1);

            // Definir destinatários
            let targetRolesText = 'Todos';
            if (alert.target_roles !== 'all') {
                targetRolesText = alert.target_roles.split(',').join(', ');
            }
            document.getElementById('view_target_roles').textContent = targetRolesText;

            // Definir período
            let periodText = 'Sempre visível';
            if (alert.start_date && alert.end_date) {
                const startDate = new Date(alert.start_date);
                const endDate = new Date(alert.end_date);
                periodText = `${startDate.toLocaleDateString()} até ${endDate.toLocaleDateString()}`;
            } else if (alert.start_date) {
                const startDate = new Date(alert.start_date);
                periodText = `A partir de ${startDate.toLocaleDateString()}`;
            } else if (alert.end_date) {
                const endDate = new Date(alert.end_date);
                periodText = `Até ${endDate.toLocaleDateString()}`;
            }
            document.getElementById('view_period').textContent = periodText;

            // Abrir o modal
            const bsModal = new bootstrap.Modal(viewModal);
            bsModal.show();
        }

        // Função para formatar data
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString();
        }

        // Carregar alertas ao iniciar e configurar atualização
        document.addEventListener('DOMContentLoaded', function() {
            // Carregar alertas inicialmente
            loadUserAlerts();

            // Recarregar alertas quando o dropdown for aberto
            const notificationsDropdown = document.getElementById('notificationsDropdown');
            if (notificationsDropdown) {
                notificationsDropdown.addEventListener('click', function() {
                    loadUserAlerts();
                });
            }

            // Recarregar alertas a cada 5 minutos
            setInterval(loadUserAlerts, 5 * 60 * 1000);
        });
    </script>
    <!-- Page Specific Scripts -->
    <?= render_scripts() ?>
</body>

</html>