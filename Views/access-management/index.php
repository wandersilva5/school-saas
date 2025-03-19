<!-- Content -->
<div class="row">
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
        <div class="card-heard">
            <div class="d-flex justify-content-between align-items-center mt-2">
                <h1 class="h3 mb-0">Gerenciar Acessos</h1>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 ms-auto">
                    <!-- Botão para abrir o modal -->
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                        <i class="fas fa-user-plus"></i> Criar Novo Acesso
                    </button>
                </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class=" table-primary">
                        <tr>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Email</th>
                            <th>Instituição</th>
                            <th>Data de Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['institution_name']) ?></td>
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
                    <?php if ($totalPages > 1): ?>
                        <!-- Botão Anterior -->
                        <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= ($currentPage <= 1) ? '#' : '?page=' . ($currentPage - 1) ?>"
                                tabindex="-1">
                                Anterior
                            </a>
                        </li>

                        <!-- Números das Páginas -->
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Botão Próximo -->
                        <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link"
                                href="<?= ($currentPage >= $totalPages) ? '#' : '?page=' . ($currentPage + 1) ?>">
                                Próximo
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <!-- end Paginação -->
        </div>
    </div>

    <!-- Mais cards... -->
</div>

<!-- Modal de Novo Usuário -->
<!-- Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Cadastro de Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="userForm" method="POST" action="/access-management/create-user" class="needs-validation"
                    novalidate>
                    <!-- Campo Nome -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="name" name="name" required minlength="3" maxlength="100"
                            placeholder="Digite o nome completo">
                        <div class="invalid-feedback">
                            Por favor, informe um nome válido.
                        </div>
                    </div>

                    <!-- Campo Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">
                            Por favor, informe um email válido.
                        </div>
                    </div>

                    <!-- Campo Perfil -->
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Perfil</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="">Selecione um perfil...</option>
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role['id']) ?>"
                                        data-description="<?= htmlspecialchars($role['description']) ?>">
                                        <?= htmlspecialchars($role['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="invalid-feedback">
                            Por favor, selecione um perfil.
                        </div>
                        <small id="roleDescription" class="form-text text-muted mt-1"></small>
                    </div>

                    <div class="mb-3">
                        <label for="institution" class="form-label">Instituição</label>
                        <select class="form-select" id="institution" name="institution_id" required>
                            <option value="">Selecione uma instituição</option>
                            <?php foreach ($institutions as $institution): ?>
                                <option value="<?= $institution['id'] ?>"
                                    <?= ($institution['id'] == $_SESSION['user']['institution_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($institution['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Por favor, selecione uma instituição.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="userForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição de Perfis (existente) -->
<div class="modal fade" id="editRolesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/access-management/update-roles" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Perfis - <span id="userName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="userId">
                    <label for="role_id" class="form-label">Perfil</label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <option value="">Selecione um perfil...</option>
                        <?php if (!empty($roles)): ?>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= htmlspecialchars($role['id']) ?>"
                                    data-description="<?= htmlspecialchars($role['description']) ?>">
                                    <?= htmlspecialchars($role['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php push('scripts') ?>
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

    // Função para preservar parâmetros na paginação
    document.querySelectorAll('.pagination .page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('href') !== '#') {
                e.preventDefault();
                const currentUrl = new URL(window.location.href);
                const newPage = new URLSearchParams(this.getAttribute('href')).get('page');
                currentUrl.searchParams.set('page', newPage);
                window.location.href = currentUrl.toString();
            }
        });
    });
</script>
<?php endpush() ?>