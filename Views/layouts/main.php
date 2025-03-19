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
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
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
    <!-- Page Specific Scripts -->
    <?= render_scripts() ?>
    </script>
</body>

</html>