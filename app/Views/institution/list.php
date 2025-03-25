<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instituições | Portal Escolar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2e59d9 0%, #2e59d9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            padding: 2rem;
            z-index: 10;
        }

        .page-title {
            color: white;
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 300;
            letter-spacing: 1px;
        }

        .institution-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .institution-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: none;
            padding: 1.5rem;
            text-align: center;
        }

        .logo-container {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            border: 5px solid #f0f0f0;
            overflow: hidden;
        }

        .institution-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-body {
            padding: 1.5rem;
            text-align: center;
        }

        .institution-name {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .institution-domain {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .institution-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 1.5rem;
        }

        .stat {
            text-align: center;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2e59d9;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-access {
            width: 100%;
            padding: 1rem;
            font-weight: 600;
            border-radius: 30px;
            transition: all 0.3s ease;
            background-color: #2e59d9;
            border-color: #2e59d9;
        }

        .btn-access:hover {
            background-color: #1e46af;
            border-color: #1e46af;
            transform: translateY(-2px);
        }

        .no-institutions {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .hero-wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            opacity: 0.1;
            z-index: -1;
        }

        .badge-active {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: #28a745;
            color: white;
            font-size: 0.7rem;
            padding: 0.4rem 0.8rem;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
        }

        .badge-inactive {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: #dc3545;
            color: white;
            font-size: 0.7rem;
            padding: 0.4rem 0.8rem;
            border-radius: 30px;
            font-weight: 600;
            box-shadow: 0 3px 10px rgba(220, 53, 69, 0.3);
        }

        .back-button {
            color: white;
            position: absolute;
            top: 1rem;
            left: 1rem;
            font-size: 1.25rem;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            color: rgba(255, 255, 255, 0.8);
            transform: translateX(-3px);
        }
    </style>
</head>

<body>
    <a href="/logout" class="back-button">
        <i class="bi bi-arrow-left-circle-fill"></i>
    </a>

    <div class="container py-4">
        <h1 class="page-title">Suas Instituições</h1>

        <div class="row">
            <?php if (!empty($institutions)): ?>
                <?php foreach ($institutions as $institution): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="institution-card">
                            <?php if ($institution['active']): ?>
                                <span class="badge-active">Ativa</span>
                            <?php else: ?>
                                <span class="badge-inactive">Inativa</span>
                            <?php endif; ?>

                            <div class="card-header">
                                <div class="logo-container">
                                    <?php if (!empty($institution['logo_url'])): ?>
                                        <img src="<?= base_url($institution['logo_url']) ?>" alt="<?= htmlspecialchars($institution['name']) ?>" class="institution-logo">
                                    <?php else: ?>
                                        <i class="bi bi-building" style="font-size: 3rem; color: #2e59d9;"></i>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="card-body">
                                <h3 class="institution-name"><?= htmlspecialchars($institution['name']) ?></h3>
                                <p class="institution-domain"><?= htmlspecialchars($institution['name']) ?>.portalescolar.com.br</p>

                                <div class="institution-stats">
                                    <div class="stat">
                                        <div class="stat-value"><?= isset($institution['students_count']) ? $institution['students_count'] : 0 ?></div>
                                        <div class="stat-label">Alunos</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-value"><?= isset($institution['classes_count']) ? $institution['classes_count'] : 0 ?></div>
                                        <div class="stat-label">Turmas</div>
                                    </div>
                                    <div class="stat">
                                        <div class="stat-value"><?= isset($institution['teachers_count']) ? $institution['teachers_count'] : 0 ?></div>
                                        <div class="stat-label">Professores</div>
                                    </div>
                                </div>

                                <a href="/institution/select/<?= $institution['id'] ?>" class="btn btn-primary btn-access">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Acessar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="no-institutions">
                        <i class="bi bi-emoji-frown" style="font-size: 4rem; color: #6c757d; margin-bottom: 1rem;"></i>
                        <h3>Nenhuma instituição encontrada</h3>
                        <p class="text-muted">Você não possui instituições vinculadas à sua conta.</p>
                        <a href="/contact" class="btn btn-primary mt-3">Solicitar acesso</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <svg class="hero-wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
        <path fill="#ffffff" d="M0,96L48,112C96,128,192,160,288,165.3C384,171,480,149,576,128C672,107,768,85,864,96C960,107,1056,149,1152,154.7C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
    </svg>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>