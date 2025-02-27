<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            min-height: 100vh;
            background: #154A9A;
            color: white;
            width: 250px;
            position: fixed;
            left: 0;
            top: 0;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .08);
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .05);
        }

        .card-stats {
            transition: transform 0.3s;
        }

        .card-stats:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            font-size: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>
    <div class="main-content">
        <?php require_once __DIR__ . '/../layouts/header.php'; ?>

        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Gerenciar Acessos</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newUserModal">
                    <i class="bi bi-person-plus"></i> Novo Usuário
                </button>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    Operação realizada com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Perfis</th>
                                    <th>Data de Criação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user['name']) ?></td>
                                        <td><?= htmlspecialchars($user['email']) ?></td>
                                        <td>
                                            <?php
                                            $rolesList = explode(',', $user['roles'] ?? '');
                                            foreach ($rolesList as $role):
                                                if (!empty($role)):
                                            ?>
                                                    <span class="badge bg-primary"><?= htmlspecialchars($role) ?></span>
                                            <?php
                                                endif;
                                            endforeach;
                                            ?>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary"
                                                onclick="editRoles(<?= $user['id'] ?>, '<?= htmlspecialchars($user['name']) ?>', '<?= htmlspecialchars($user['roles'] ?? '') ?>')">
                                                <i class="bi bi-pencil"></i> Editar Perfis
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1">Anterior</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Próximo</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

        <?php require_once __DIR__ . '/../layouts/footer.php'; ?>
    </div>

    <!-- Modal de Novo Usuário -->
    <div class="modal fade" id="newUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/access-management/create-user" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Novo Usuário</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Perfis</label>
                            <?php foreach ($roles as $role): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        name="roles[]" value="<?= $role['id'] ?>"
                                        id="newRole<?= $role['id'] ?>">
                                    <label class="form-check-label" for="newRole<?= $role['id'] ?>">
                                        <?= htmlspecialchars($role['name']) ?>
                                        <small class="text-muted d-block">
                                            <?= htmlspecialchars($role['description']) ?>
                                        </small>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar Usuário</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Edição de Perfis (existente) -->
    <div class="modal fade" id="editRolesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/access-management/update-roles" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Perfis - <span id="userName"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="userId">
                        <?php foreach ($roles as $role): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    name="roles[]" value="<?= $role['id'] ?>"
                                    id="role<?= $role['id'] ?>">
                                <label class="form-check-label" for="role<?= $role['id'] ?>">
                                    <?= htmlspecialchars($role['name']) ?>
                                    <small class="text-muted d-block">
                                        <?= htmlspecialchars($role['description']) ?>
                                    </small>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editRoles(userId, userName, currentRoles) {
            document.getElementById('userId').value = userId;
            document.getElementById('userName').textContent = userName;

            // Limpa todas as checkboxes
            document.querySelectorAll('#editRolesModal input[type="checkbox"]').forEach(cb => cb.checked = false);

            // Marca as checkboxes dos roles atuais
            if (currentRoles) {
                const roles = currentRoles.split(',');
                roles.forEach(role => {
                    const cb = document.querySelector(`#editRolesModal input[value="${role.trim()}"]`);
                    if (cb) cb.checked = true;
                });
            }

            new bootstrap.Modal(document.getElementById('editRolesModal')).show();
        }
    </script>
</body>

</html>