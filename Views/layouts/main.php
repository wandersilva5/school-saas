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

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/assets/js/script.js"></script>
</body>
</html>