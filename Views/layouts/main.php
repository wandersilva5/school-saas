<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Portal Escolar' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="wrapper">
            <!-- Sidebar -->
            <?php include 'sidebar.php'; ?>

            <div class="main">
                <!-- Header -->
                <?php include 'header.php'; ?>

                <!-- Content -->
                <main class="content">
                    <div class="container-fluid p-4">
                        <?php echo $this->renderView($view, $data); ?>
                    </div>
                </main>

                <!-- Footer -->
                <?php include 'footer.php'; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Login/Register pages don't use the dashboard layout -->
        <?php echo $this->renderView($view, $data); ?>
    <?php endif; ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="/assets/js/script.js"></script>
</body>
</html>