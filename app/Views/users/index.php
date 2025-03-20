<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h4 class="card-title"><?= $pageTitle ?></h4>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                            <i class="bi bi-plus-circle"></i> Novo Usuário
                        </button>
                    </div>
                </div>


                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Perfis</th>
                                <th>Data Cadastro</th>
                                <th>Status</th>
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
                                        $roles = explode(',', $user['roles'] ?? '');
                                        foreach ($roles as $role):
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
                                        <?php if ($user['active']): ?>
                                            <span class="badge bg-success">Ativo</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Inativo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" onclick="editUser(<?= $user['id'] ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteUser(<?= $user['id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= (int)$currentPage - 1 ?>">Anterior</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($currentPage == $i) ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">Próximo</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cadastro -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm" action="/users/store" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome</label>
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
                    <!-- Make sure roles checkboxes are properly named with array notation -->
                    <div class="mb-3">
                        <label class="form-label">Perfis</label>
                        <?php if (!empty($allRoles) && is_array($allRoles)): ?>
                            <?php foreach ($allRoles as $role): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                        value="<?= $role['id'] ?>" id="role_<?= $role['id'] ?>">
                                    <label class="form-check-label" for="role_<?= $role['id'] ?>">
                                        <?= htmlspecialchars($role['name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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

<!-- Modal de Edição -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" action="/users/update" method="POST">
                    <input type="hidden" id="edit_user_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Senha (deixe em branco para manter)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_active" name="active" value="1">
                        <label class="form-check-label" for="edit_active">Ativo</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Perfis</label>
                        <?php if (!empty($allRoles) && is_array($allRoles)): ?>
                            <?php foreach ($allRoles as $role): ?>
                                <div class="form-check">
                                    <input class="form-check-input edit-role" type="checkbox" name="roles[]"
                                        value="<?= $role['id'] ?>" id="edit_role_<?= $role['id'] ?>">
                                    <label class="form-check-label" for="edit_role_<?= $role['id'] ?>">
                                        <?= htmlspecialchars($role['name']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="editUserForm" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </div>
</div>

<?php push('scripts') ?>
<script>
    async function editUser(userId) {
        try {
            const response = await fetch(`/users/show/${userId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(await response.text());
            }

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            // Update form action with correct ID
            document.getElementById('editUserForm').action = `/users/update/${data.id}`;

            // Fill form fields
            document.getElementById('edit_user_id').value = data.id;
            document.getElementById('edit_name').value = data.name;
            document.getElementById('edit_email').value = data.email;
            document.getElementById('edit_active').checked = Boolean(parseInt(data.active));

            // Clear password field - it should be empty for editing
            document.getElementById('edit_password').value = '';

            // Handle roles
            document.querySelectorAll('.edit-role').forEach(cb => cb.checked = false);
            if (data.roles) {
                data.roles.forEach(roleId => {
                    const cb = document.getElementById(`edit_role_${roleId}`);
                    if (cb) cb.checked = true;
                });
            }

            // Open modal
            const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
            modal.show();

        } catch (error) {
            console.error('Error:', error);
            alert('Error loading user data: ' + error.message);
        }
    }

    async function deleteUser(userId) {
        if (confirm('Tem certeza que deseja excluir este usuário?')) {
            window.location.href = `/users/delete/${userId}`;
        }
    }
</script>
<?php endpush() ?>